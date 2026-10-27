// Kalpana's live mascot: an inline SVG robot that floats, blinks, follows the pointer and reacts to
// what the assistant is doing (idle, listening, thinking, asking, happy, sad). Purely decorative.
(() => {
  const NS = 'http://www.w3.org/2000/svg';
  let count = 0;
  const node = (tag, attrs = {}, parent) => { const el = document.createElementNS(NS, tag); for (const [k, v] of Object.entries(attrs)) el.setAttribute(k, v); if (parent) parent.append(el); return el; };
  const MOUTHS = { idle: 'M50 63 Q60 70 70 63', listening: 'M52 64 Q60 68 68 64', thinking: 'M53 66 L67 64', asking: 'M55 65 Q60 61 65 65 Q60 69 55 65', happy: 'M47 61 Q60 75 73 61 Z', sad: 'M50 68 Q60 61 70 68' };
  const TIPS = ['Tell me your board, students and room size. I’ll handle the rest!', 'You can just talk to me with the mic button.', 'Try “make it materials only” after a plan appears.', 'I size equipment per batch, not per student. It saves a lot!', 'Every price you see is calculated, never guessed.'];

  function mount(container, { size = 96, bubble = null, greeting = '' } = {}) {
    if (!container) return null;
    const id = `km${++count}`;
    const svg = node('svg', { viewBox: '0 0 120 140', width: size, height: Math.round(size * 140 / 120), class: 'mascot idle', role: 'img', 'aria-label': 'Kalpana, your assistant' });
    const defs = node('defs', {}, svg);
    const grad = (gid, stops) => { const g = node('linearGradient', { id: gid, x1: 0, y1: 0, x2: 1, y2: 1 }, defs); stops.forEach(([o, c]) => node('stop', { offset: o, 'stop-color': c }, g)); };
    grad(`${id}-head`, [[0, '#a78bfa'], [0.55, '#7c5ce0'], [1, '#22b8d6']]);
    grad(`${id}-body`, [[0, '#8b6cf0'], [1, '#5b8de8']]);
    grad(`${id}-face`, [[0, '#2a2350'], [1, '#191433']]);
    const glow = node('radialGradient', { id: `${id}-glow` }, defs); node('stop', { offset: 0, 'stop-color': '#fff6a8' }, glow); node('stop', { offset: 1, 'stop-color': '#ffb84d' }, glow);

    node('ellipse', { cx: 60, cy: 132, rx: 26, ry: 5, class: 'm-shadow' }, svg);
    const float = node('g', { class: 'm-float' }, svg);
    const thoughts = node('g', { class: 'm-thoughts' }, float);
    [0, 1, 2].forEach(i => node('circle', { cx: 46 + i * 14, cy: 2, r: 3.6, class: `m-dot m-dot-${i}` }, thoughts));
    const sparkles = node('g', { class: 'm-sparkles' }, float);
    [[14, 30], [104, 26], [10, 78], [110, 74], [60, 2]].forEach(([x, y], i) => node('path', { d: `M${x} ${y - 6} L${x + 2} ${y - 2} L${x + 6} ${y} L${x + 2} ${y + 2} L${x} ${y + 6} L${x - 2} ${y + 2} L${x - 6} ${y} L${x - 2} ${y - 2} Z`, class: `m-spark m-spark-${i}` }, sparkles));
    const question = node('text', { x: 98, y: 22, class: 'm-question' }, float); question.textContent = '?';

    const armL = node('g', { class: 'm-arm m-arm-left' }, float); node('rect', { x: 18, y: 86, width: 11, height: 28, rx: 5.5, fill: `url(#${id}-body)` }, armL);
    const armR = node('g', { class: 'm-arm m-arm-right' }, float); node('rect', { x: 91, y: 86, width: 11, height: 28, rx: 5.5, fill: `url(#${id}-body)` }, armR);
    node('rect', { x: 30, y: 80, width: 60, height: 44, rx: 16, fill: `url(#${id}-body)` }, float);
    node('rect', { x: 42, y: 90, width: 36, height: 20, rx: 6, fill: '#ffffff30' }, float);
    node('path', { d: 'M45 100 L52 100 L55 94 L59 106 L63 97 L66 100 L75 100', class: 'm-pulse' }, float);

    const head = node('g', { class: 'm-head' }, float);
    node('line', { x1: 60, y1: 22, x2: 60, y2: 12, stroke: '#8b6cf0', 'stroke-width': 3, 'stroke-linecap': 'round' }, head);
    node('circle', { cx: 60, cy: 10, r: 5, fill: `url(#${id}-glow)`, class: 'm-antenna' }, head);
    node('rect', { x: 14, y: 40, width: 8, height: 18, rx: 4, fill: '#7c5ce0' }, head);
    node('rect', { x: 98, y: 40, width: 8, height: 18, rx: 4, fill: '#22b8d6' }, head);
    node('rect', { x: 20, y: 22, width: 80, height: 58, rx: 22, fill: `url(#${id}-head)` }, head);
    node('rect', { x: 28, y: 30, width: 64, height: 42, rx: 15, fill: `url(#${id}-face)` }, head);
    node('ellipse', { cx: 40, cy: 30, rx: 12, ry: 4, fill: '#ffffff40' }, head);
    const eyes = node('g', { class: 'm-eyes' }, head);
    const pupils = node('g', { class: 'm-pupils' }, eyes);
    for (const x of [46, 74]) { node('ellipse', { cx: x, cy: 50, rx: 7, ry: 8, class: 'm-eye' }, pupils); node('circle', { cx: x + 2, cy: 47, r: 2.2, fill: '#fff' }, pupils); }
    const happyEyes = node('g', { class: 'm-happy-eyes' }, head);
    for (const x of [46, 74]) node('path', { d: `M${x - 7} 52 Q${x} 42 ${x + 7} 52`, class: 'm-arc' }, happyEyes);
    node('circle', { cx: 36, cy: 62, r: 4, class: 'm-cheek' }, head); node('circle', { cx: 84, cy: 62, r: 4, class: 'm-cheek' }, head);
    const mouth = node('path', { d: MOUTHS.idle, class: 'm-mouth' }, head);
    container.replaceChildren(svg);

    let state = 'idle', reset, tipIndex = 0;
    const setState = (next, { hold = 0 } = {}) => {
      clearTimeout(reset); state = next;
      svg.setAttribute('class', `mascot ${next}`); if (next === 'thinking') pupils.style.transform = 'translate(3px, -4px)'; mouth.setAttribute('d', MOUTHS[next] || MOUTHS.idle);
      if (hold) reset = setTimeout(() => setState('idle'), hold);
    };
    const say = text => { if (!bubble) return; bubble.textContent = text; bubble.classList.remove('show'); void bubble.offsetWidth; bubble.classList.add('show'); };
    // Eyes follow the pointer, within the face screen.
    const follow = event => {
      if (state === 'thinking') return;
      const box = svg.getBoundingClientRect(); const dx = event.clientX - (box.left + box.width / 2); const dy = event.clientY - (box.top + box.height * 0.36);
      const distance = Math.hypot(dx, dy) || 1; const reach = Math.min(1, distance / 300);
      pupils.style.transform = `translate(${(dx / distance) * 5 * reach}px, ${(dy / distance) * 4 * reach}px)`;
    };
    window.addEventListener('pointermove', follow, { passive: true });
    svg.addEventListener('click', () => { setState('happy', { hold: 1600 }); say(TIPS[tipIndex++ % TIPS.length]); });
    if (greeting) setTimeout(() => { setState('happy', { hold: 1400 }); say(greeting); }, 500);
    return { setState, say, get state() { return state; } };
  }
  window.KalpanaMascot = { mount };
})();
