const status = document.querySelector('#status');
document.querySelector('#launch').onclick = async () => {
  const tab = window.open('about:blank', '_blank');
  if (tab) tab.opener = null;
  try {
    const response = await fetch('/launch', { method: 'POST' }); const body = await response.json();
    if (!response.ok) throw Error(body.error);
    if (tab) tab.location.replace(body.launchUrl); else location.assign(body.launchUrl);
    status.textContent = 'Complete your plan in Kalpana, then fetch the JSON here.';
  } catch (error) { tab?.close(); status.textContent = error.message; }
};
document.querySelector('#result').onclick = async () => {
  try {
    const response = await fetch('/result'); const body = await response.json();
    if (!response.ok) throw Error(body.error);
    status.textContent = `Session status: ${body.status}`;
    document.querySelector('#output').textContent = JSON.stringify(body, null, 2);
  } catch (error) { status.textContent = error.message; }
};
