
          <div style="position:relative;" id="sks-customise-wrap">
            <button type="button" id="sks-customise-toggle" aria-expanded="false" aria-controls="sks-customise-menu"
               style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;background:#f4742b;color:#fff;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .2s;white-space:nowrap;">
              <i class="fa-solid fa-sliders" style="font-size:11px;"></i> Customise This Lab
              <i class="fa-solid fa-chevron-down" style="font-size:10px;margin-left:2px;"></i>
            </button>
            <div id="sks-customise-menu"
                 style="display:none;position:absolute;right:0;bottom:calc(100% + 8px);min-width:230px;background:#fff;border-radius:12px;box-shadow:0 12px 32px rgba(0,0,0,.18);overflow:hidden;z-index:50;">
              <a id="sks-opt-cart" href="<?php echo e(url('/sso/shop')); ?>?target=%2Fskillvation-cart"
                 style="display:flex;align-items:center;gap:10px;padding:13px 16px;color:#0b2545;font-size:13px;font-weight:600;text-decoration:none;border-bottom:1px solid #eef1f6;transition:background .15s;"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                <i class="fa-solid fa-cart-shopping" style="color:#f4742b;width:16px;"></i>
                <span>Buy Standard Skillvation Lab</span>
              </a>
              <a id="sks-opt-design" href="#dml-modal" role="button"
                 style="display:flex;align-items:center;gap:10px;padding:13px 16px;color:#0b2545;font-size:13px;font-weight:600;text-decoration:none;transition:background .15s;cursor:pointer;"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                <i class="fa-solid fa-pen-ruler" style="color:#2563eb;width:16px;"></i>
                <span>Design My Lab</span>
              </a>
            </div>
          </div>
      
      <div id="dml-modal" role="dialog" aria-modal="true" aria-labelledby="dml-title" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(11,37,69,.55);align-items:center;justify-content:center;padding:16px;">
        <div id="dml-box" style="background:#fff;border-radius:16px;max-width:760px;width:100%;max-height:88vh;overflow:auto;box-shadow:0 24px 60px rgba(0,0,0,.3);display:flex;flex-direction:column;">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #eef1f6;position:sticky;top:0;background:#fff;">
            <strong id="dml-title" style="font-size:16px;color:#0b2545;"><i class="fa-solid fa-pen-ruler" style="color:#2563eb;"></i>&nbsp; Design My Lab</strong>
            <button type="button" id="dml-close" aria-label="Close Design My Lab" style="background:none;border:none;font-size:20px;color:#64748b;cursor:pointer;line-height:1;">&times;</button>
          </div>
          <div id="dml-ready" style="display:none;align-items:center;justify-content:space-between;gap:12px;padding:10px 22px;background:#ecfdf5;border-bottom:1px solid #a7f3d0;font-size:13px;color:#065f46;font-weight:600;">
            <span><i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>&nbsp; Your lab is confirmed. Taking you to your cart…</span>
            <a id="dml-go-cart" href="#" style="padding:7px 14px;border-radius:8px;background:#065f46;color:#fff;font-size:12px;font-weight:700;text-decoration:none;">Go to cart now</a>
          </div>
          <div id="dml-frame-wrap" style="display:none;flex:1;min-height:0;">
            <iframe id="dml-frame" title="Design My Lab" style="width:100%;height:100%;border:0;display:block;" allow="microphone"></iframe>
          </div>
          <div id="dml-body" style="padding:22px;">
            
          </div>
        </div>
      </div>

<?php $__env->startPush('scripts'); ?>
<script>
// ── "Customise This Lab" dropdown toggle ──────────────────────────
(function(){
  var toggle = document.getElementById('sks-customise-toggle');
  var menu   = document.getElementById('sks-customise-menu');
  if(!toggle || !menu) return;
  toggle.addEventListener('click', function(e){
    e.stopPropagation();
    var expanded = menu.style.display !== 'block';
    menu.style.display = expanded ? 'block' : 'none';
    toggle.setAttribute('aria-expanded', String(expanded));
  });
  document.addEventListener('click', function(e){
    if(!menu.contains(e.target) && e.target !== toggle){ menu.style.display = 'none'; toggle.setAttribute('aria-expanded', 'false'); }
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){ menu.style.display = 'none'; toggle.setAttribute('aria-expanded', 'false'); }
  });
})();

// ── "Design My Lab" — start session, poll result, show + save ─────
(function(){
  var START_URL = '<?php echo e(route("lab-design.start")); ?>';
  var RESULT_URL = '<?php echo e(url("/lab-design/result")); ?>'; // + /{sessionId}
  var CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '<?php echo e(csrf_token()); ?>';

  var openBtn = document.getElementById('sks-opt-design');
  var modal   = document.getElementById('dml-modal');
  var body    = document.getElementById('dml-body');
  var closeBtn= document.getElementById('dml-close');
  if(!openBtn || !modal || !body) return;

  var box       = document.getElementById('dml-box');
  var frameWrap = document.getElementById('dml-frame-wrap');
  var frame     = document.getElementById('dml-frame');
  var readyBar  = document.getElementById('dml-ready');
  var goCart    = document.getElementById('dml-go-cart');
  var CART_URL  = '<?php echo e(url("/club-shop/lab-design-cart")); ?>';

  var previousOverflow = '';
  function openModal(){ previousOverflow = document.body.style.overflow; modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; if(closeBtn) closeBtn.focus(); }
  function closeModal(){ modal.style.display = 'none'; document.body.style.overflow = previousOverflow; activeSession = null; startAttempt++; frame.src = 'about:blank'; openBtn.focus(); }
  // Kalpana runs inside the modal; the modal widens while it is shown.
  function showFrame(url){
    box.style.maxWidth = '1200px'; box.style.height = '88vh'; box.style.overflow = 'hidden';
    frame.src = url; frameWrap.style.display = 'block'; body.style.display = 'none';
  }
  function showBody(){
    box.style.maxWidth = '760px'; box.style.height = ''; box.style.overflow = 'auto';
    frameWrap.style.display = 'none'; readyBar.style.display = 'none'; body.style.display = 'block';
  }
  function resetModal(){ frame.src = 'about:blank'; showBody(); }

  if(closeBtn) closeBtn.addEventListener('click', closeModal);
  // A stray backdrop click must not discard a design in progress; use the × button instead.
  modal.addEventListener('click', function(e){ if(e.target === modal && frameWrap.style.display === 'none') closeModal(); });

  function setBody(html){ body.innerHTML = html; }
  function esc(s){ return String(s==null?'':s).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }

  // Kalpana interviews the visitor and only reports back once they confirm:
  // "kalpana:quote" fills this page's proposal form with the design; "kalpana:cart"/"kalpana:checkout" open
  // the shop cart or checkout. The confirmed design is fetched (and saved) through the lab-design result first.
  var activeSession = null;
  var startAttempt = 0;
  window.addEventListener('message', function(e){
    var d = e.data || {};
    if (e.source !== frame.contentWindow || !activeSession || d.sessionId !== activeSession || ['kalpana:quote', 'kalpana:cart', 'kalpana:checkout'].indexOf(d.type) < 0) return;
    try { if (e.origin !== new URL(frame.src).origin) return; } catch (err) { return; }
    var sessionId = activeSession;
    var result = fetch(RESULT_URL + '/' + encodeURIComponent(sessionId), {headers:{'Accept':'application/json'}}).then(function(r){ return r.json(); });
    if (d.type === 'kalpana:quote') {
      result.then(function(res){
        if (!res || res.result !== 1 || !res.data) throw new Error((res && res.message) || 'not ready');
        attachDesign(res.data, sessionId);
        closeModal();
        var target = document.getElementById('cta-section');
        if (target) target.scrollIntoView({behavior: 'smooth', block: 'start'});
        setTimeout(function(){ var school = document.querySelector('#planEnquiryForm [name="school"]'); if (school) school.focus({preventScroll: true}); }, 700);
      }).catch(function(){ alert('Your design could not be attached. Please try Request quotation again.'); });
      return;
    }
    var target = '/lab-design-cart?session=' + encodeURIComponent(sessionId) + (d.type === 'kalpana:checkout' ? '&next=checkout' : '');
    var url = '<?php echo e(url("/sso/shop")); ?>?target=' + encodeURIComponent(target);
    goCart.href = url;
    readyBar.style.display = 'flex';
    var go = function(){ window.location.href = url; };
    result.then(go, go);
  });

  // Fills the "Get Your Skill Lab Proposal" form with a confirmed Kalpana design: a readable summary in the
  // message box and the itemized quotation (sent to the team with the enquiry).
  var ROMAN = {6:'VI',7:'VII',8:'VIII',9:'IX',10:'X',11:'XI',12:'XII'};
  var SCOPE = {infrastructure_materials:'Complete lab (room fit-out + materials)', materials_only:'Materials only', existing_room:'Existing lab room'};
  function inr(minor){ return '₹' + (Number(minor || 0) / 100).toLocaleString('en-IN', {maximumFractionDigits: 2}); }
  function attachDesign(data, sessionId){
    var form = document.getElementById('planEnquiryForm');
    if (!form) return;
    var plan = data.plan || {}, quote = data.quotation || {}, profile = plan.profile || {}, lines = quote.lines || [];
    var gst = Math.round((quote.total || 0) * (plan.gstBasisPoints || 0) / 10000);
    var classes = (profile.classes || []).map(function(c){ return ROMAN[c] || c; });
    var areas = (plan.requiredItems || []).filter(function(item){ return / · (Basic|Advanced|Premium)$/.test(item.name); }).map(function(item){ return item.name.replace(' · ', ' (') + ')'; });
    var summary = [
      'Lab designed with Kalpana (reference ' + String(sessionId).slice(0, 8) + ')',
      [profile.board, classes.length ? 'Classes ' + classes[0] + (classes.length > 1 ? '–' + classes[classes.length - 1] : '') : '', profile.totalStudents ? profile.totalStudents + ' students' : '', (profile.rooms || []).map(function(r){ return r.areaSqFt + ' sq ft'; }).join(' + '), SCOPE[profile.setup] || ''].filter(Boolean).join(' · '),
      'Overall level: ' + (plan.tier ? plan.tier.charAt(0).toUpperCase() + plan.tier.slice(1) : '—') + (areas.length ? ' · Skill areas: ' + areas.join(', ') : ''),
      'Products: ' + lines.map(function(l){ return l.name + ' × ' + l.quantity; }).join('; '),
      'Estimated total: ' + inr(quote.total) + (gst ? ' + GST ' + inr(gst) + ' = ' + inr(quote.total + gst) : '')
    ].join('\n');
    var set = function(selector, value){ var el = form.querySelector(selector); if (el) el.value = value; };
    set('[name="address"]', summary);
    var board = form.querySelector('[name="message"]');
    if (board && Array.from(board.options).some(function(option){ return option.value === profile.board; })) board.value = profile.board;
    set('#planTitle', 'Skillvation Lab — Kalpana design – ' + (plan.title || 'designed lab'));
    set('#planQuotation', JSON.stringify({
      source: 'kalpana', session_id: sessionId, gst_estimate: gst / 100,
      selected_components: lines.map(function(l){ return { title: l.name, category: l.kind === 'course' ? 'MySkool course' : (l.name.split(' – ')[1] ? l.name.split(' – ')[0] : 'Lab essentials'), qty: l.quantity, unit_price: l.unitAmount / 100, subtotal: l.lineAmount / 100 }; })
    }));
    form.dataset.kalpana = sessionId;
    var note = document.getElementById('kalpanaAttached');
    if (!note) {
      note = document.createElement('div'); note.id = 'kalpanaAttached';
      note.style.cssText = 'margin:0 0 16px;padding:12px 14px;border-radius:12px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;font-size:13px;line-height:1.6;';
      form.parentNode.insertBefore(note, form);
    }
    note.innerHTML = '<strong><i class="fa-solid fa-circle-check"></i>&nbsp; Your Kalpana design is attached</strong><br>' + esc(lines.length + ' products · estimated ' + inr(quote.total + gst) + (gst ? ' incl. GST' : '')) + '. Add your details below and request the quotation. <a href="#" id="kalpanaReopen" style="color:#047857;font-weight:700;text-decoration:underline;">Change the design</a>';
    document.getElementById('kalpanaReopen').onclick = function(ev){ ev.preventDefault(); openBtn.click(); };
  }

  openBtn.addEventListener('click', function(event){
    event.preventDefault();
    var dropdownMenu = document.getElementById('sks-customise-menu');
    if(dropdownMenu) dropdownMenu.style.display = 'none';
    document.getElementById('sks-customise-toggle').setAttribute('aria-expanded', 'false');
    activeSession = null;
    var attempt = ++startAttempt;
    resetModal();
    openModal();
    setBody('<div style="text-align:center;padding:30px;color:#475569;"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:22px;color:#2563eb;"></i><p style="margin-top:12px;font-size:13px;">Starting your design session…</p></div>');

    fetch(START_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({ reference: 'skillvation-lab', source: 'labs/stem' })
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (attempt !== startAttempt) return;
      if(!res || res.result !== 1 || !res.session_id || !res.launch_url){
        setBody('<div style="text-align:center;padding:30px;color:#b91c1c;"><i class="fa-solid fa-triangle-exclamation" style="font-size:22px;"></i><p style="margin-top:12px;font-size:13px;">'+esc((res&&res.message)||'Could not start the design session.')+'</p></div>');
        return;
      }
      activeSession = res.session_id;
      showFrame(res.launch_url);
    })
    .catch(function(){
      if (attempt !== startAttempt) return;
      setBody('<div style="text-align:center;padding:30px;color:#b91c1c;">Network error while starting the session.</div>');
    });
  });
})();

</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/html/pro/resources/views/frontend/home-four/partials/skillvation-lab-customisation.blade.php ENDPATH**/ ?>