// Animated diagrams drawn from the verified plan and quote. They visualize server numbers only.
(() => {
  const NS = 'http://www.w3.org/2000/svg';
  const svgNode = (tag, attrs = {}, parent) => { const el = document.createElementNS(NS, tag); for (const [k, v] of Object.entries(attrs)) el.setAttribute(k, v); if (parent) parent.append(el); return el; };
  const html = (tag, className, text) => { const el = document.createElement(tag); if (className) el.className = className; if (text !== undefined) el.textContent = text; return el; };
  const PALETTE = ['#7c5ce0', '#22b8d6', '#d66bd1', '#f2a541', '#3fb28a', '#e5677d', '#5b8de8', '#a3a0b5'];

  function activeRooms(plan) {
    const { profile } = plan;
    if (profile.layout === 'single') return [{ index: 0, ...profile.rooms[0] }];
    return profile.rooms.map((room, index) => ({ index, ...room })).filter(room => room.index === 0 ? profile.classes.some(c => c <= 10) : profile.classes.some(c => c >= 11));
  }

  // Floor plan: each active room scaled to its area, stations placed desk by desk, minimum area dashed.
  function floorPlan(plan) {
    const wrap = html('div', 'floor-plans');
    const rooms = activeRooms(plan);
    const minimum = plan.profile.board === 'CBSE' ? (plan.profile.layout === 'single' ? 600 : 400) : null;
    const largest = Math.max(...rooms.map(r => r.areaSqFt), minimum || 0);
    const dims = area => { const w = Math.sqrt(area * 4 / 3); return [w, w * 3 / 4]; };
    const [maxW] = dims(largest); const scale = 250 / maxW;
    for (const room of rooms) {
      const figure = html('figure', 'floor-plan');
      const svg = svgNode('svg', { viewBox: '0 0 300 240', role: 'img', 'aria-label': `Room ${room.index + 1} floor plan with ${plan.schedule.stationsPerRoom} stations` }, figure);
      const [w, h] = dims(room.areaSqFt).map(v => v * scale); const x = (300 - w) / 2, y = (225 - h) / 2 + 5;
      svgNode('rect', { x: 0, y: 0, width: 300, height: 240, class: 'fp-grid' }, svg);
      svgNode('rect', { x, y, width: w, height: h, rx: 8, class: 'fp-room' }, svg);
      if (minimum) { const [mw, mh] = dims(minimum).map(v => v * scale); svgNode('rect', { x: (300 - mw) / 2, y: (225 - mh) / 2 + 5, width: mw, height: mh, rx: 6, class: `fp-minimum${room.areaSqFt >= minimum ? '' : ' short'}` }, svg); }
      svgNode('path', { d: `M${x + 18} ${y + h} A16 16 0 0 1 ${x + 34} ${y + h - 16}`, class: 'fp-door' }, svg);
      const n = plan.schedule.stationsPerRoom; const cols = Math.max(1, Math.ceil(Math.sqrt(n * w / h))); const rows = Math.ceil(n / cols);
      const cellW = (w - 24) / cols, cellH = (h - 30) / rows; const desk = Math.min(cellW, cellH) * 0.62;
      for (let i = 0; i < n; i++) {
        const cx = x + 12 + cellW * (i % cols + 0.5), cy = y + 12 + cellH * (Math.floor(i / cols) + 0.5);
        const station = svgNode('g', { class: 'fp-station' }, svg); station.style.setProperty('--i', i);
        svgNode('rect', { x: cx - desk / 2, y: cy - desk / 3, width: desk, height: desk / 1.6, rx: 2.5, class: 'fp-desk' }, station);
        for (const side of [-1, 1]) svgNode('circle', { cx: cx + side * desk / 4, cy: cy + desk / 2.4, r: Math.max(1.6, desk / 7), class: 'fp-seat' }, station);
      }
      figure.append(html('figcaption', '', `Room ${room.index + 1} · ${room.areaSqFt} sq ft · ${n} stations for a batch of ${plan.schedule.batchSize}`));
      wrap.append(figure);
    }
    if (minimum) { const legend = html('p', 'fp-legend'); legend.append(html('i', ''), document.createTextNode(`Dashed outline: ${minimum} sq ft CBSE planning minimum per room (a planning check, not certification).`)); wrap.append(legend); }
    return wrap;
  }

  // Flow: how enrolment becomes equipment, the reason the quote is per batch, not per student.
  function flow(plan, countUp) {
    const steps = [
      [plan.schedule.totalStudents, 'students enrolled', '◎'],
      [plan.schedule.batches, `batches of ${plan.schedule.batchSize}`, '⧉'],
      [plan.schedule.stationsPerRoom, 'stations per room', '▦'],
      [plan.schedule.activeRooms, plan.schedule.activeRooms === 1 ? 'room equipped' : 'rooms equipped', '⌂'],
    ];
    const root = html('div', 'flow');
    steps.forEach(([value, label, icon], i) => {
      if (i) root.append(html('div', 'flow-link'));
      const step = html('div', 'flow-node'); step.style.setProperty('--i', i);
      const number = html('strong', '', '0'); step.append(html('span', 'flow-icon', icon), number, html('small', '', label));
      root.append(step); countUp(number, value, v => String(v), 900 + i * 150);
    });
    return root;
  }

  function budgetBar(total, budget, format) {
    const bar = html('div', 'budget-bar');
    const scaleMax = Math.max(total, budget) * 1.08;
    const track = html('div', 'budget-track'); const fill = html('div', `budget-fill${total > budget ? ' over' : ''}`); const marker = html('div', 'budget-marker');
    marker.style.left = `${(budget / scaleMax) * 100}%`; track.append(fill, marker);
    requestAnimationFrame(() => requestAnimationFrame(() => { fill.style.width = `${(total / scaleMax) * 100}%`; }));
    bar.append(track, html('small', '', total > budget ? `${format(total - budget)} over your budget of ${format(budget)}` : `${format(budget - total)} left of your ${format(budget)} budget`));
    return bar;
  }

  // Cost breakdown donut of the verified quote lines, with the budget gauge beneath.
  function costDonut(quote, budget, format, countUp) {
    const root = html('div', 'cost');
    const svg = svgNode('svg', { viewBox: '0 0 160 160', class: 'donut', role: 'img', 'aria-label': 'Cost breakdown' });
    const r = 62, c = 2 * Math.PI * r; let start = 0;
    svgNode('circle', { cx: 80, cy: 80, r, class: 'donut-track' }, svg);
    const legend = html('ul', 'cost-legend');
    quote.lines.forEach((line, i) => {
      const share = line.lineAmount / quote.subtotal; const length = share * c; const color = PALETTE[i % PALETTE.length];
      const seg = svgNode('circle', { cx: 80, cy: 80, r, stroke: color, 'stroke-dasharray': `${length} ${c - length}`, 'stroke-dashoffset': -start, class: 'donut-seg' }, svg);
      seg.style.setProperty('--len', length); seg.style.setProperty('--c', c); seg.style.setProperty('--i', i);
      start += length;
      const item = html('li'); item.style.setProperty('--i', i); const dot = html('i'); dot.style.background = color;
      item.append(dot, html('span', '', line.name), html('b', '', `${Math.round(share * 100)}%`)); legend.append(item);
    });
    const center = html('div', 'donut-center'); const total = html('strong', '', format(0)); center.append(total, html('small', '', 'one-time total'));
    countUp(total, quote.total, format, 1200);
    const chart = html('div', 'donut-wrap'); chart.append(svg, center);
    root.append(chart, legend);
    if (budget) root.append(budgetBar(quote.total, budget, format));
    return root;
  }

  window.KalpanaDiagrams = { floorPlan, flow, costDonut, budgetBar };
})();
