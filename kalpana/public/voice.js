// Optional hands-free input. Uses the browser's built-in speech recognition when available;
// the button stays hidden elsewhere. Transcripts go into the text field like typed input.
(() => {
  const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  window.KalpanaVoice = {
    supported: !!Recognition,
    attach(button, field, { onFinal } = {}) {
      if (!Recognition || !button || !field) { if (button) button.hidden = true; return; }
      let active = null;
      button.hidden = false;
      const idle = () => { active = null; button.classList.remove('listening'); button.setAttribute('aria-pressed', 'false'); };
      button.onclick = () => {
        if (active) { active.stop(); return; }
        const recognition = new Recognition();
        recognition.lang = navigator.language || 'en-IN'; recognition.interimResults = true; recognition.continuous = false;
        const before = field.value.trim() ? `${field.value.trim()} ` : '';
        let heard = false;
        recognition.onresult = event => {
          let text = '';
          for (const result of event.results) { text += result[0].transcript; heard = heard || result.isFinal; }
          field.value = before + text.trim(); field.dispatchEvent(new Event('input'));
        };
        recognition.onerror = () => idle();
        recognition.onend = () => { idle(); if (heard && field.value.trim() && onFinal) onFinal(); };
        try { recognition.start(); active = recognition; button.classList.add('listening'); button.setAttribute('aria-pressed', 'true'); } catch { idle(); }
      };
    },
  };
})();
