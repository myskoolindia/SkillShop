export function retrieve(db, catalog, query) {
  const ids = [...new Set(catalog.products.flatMap(p => p.sourceIds))];
  const words = [...new Set(query.toLowerCase().match(/[a-z0-9]{3,}/g) || [])].slice(0, 40);
  const chunks = [];
  for (const id of ids) {
    const doc = db.getDocument(id);
    if (!doc) continue;
    for (let start = 0; start < doc.content.length; start += 1000) {
      const excerpt = doc.content.slice(start, start + 1400);
      const lower = excerpt.toLowerCase();
      const score = words.reduce((sum, word) => sum + (lower.includes(word) ? 1 : 0), 0);
      chunks.push({ id, title: doc.title, sha256: doc.sha256, offset: start, excerpt, score });
    }
  }
  return chunks.sort((a, b) => b.score - a.score || a.offset - b.offset).slice(0, 6).map(({ score, ...chunk }) => chunk);
}
