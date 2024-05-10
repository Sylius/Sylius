export function sanitizeInput(input) {
  const div = document.createElement('div');
  div.textContent = input;
  return div.innerHTML; // Converts text content to plain HTML, stripping any scripts
}
