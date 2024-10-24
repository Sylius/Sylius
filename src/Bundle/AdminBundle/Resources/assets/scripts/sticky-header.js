(function() {
  const el = document.querySelector('[data-sticky-header]');

  const observer = new IntersectionObserver(
    ([e]) => e.target.classList.toggle('is-sticky', e.intersectionRatio < 1),
    { threshold: [1] }
  );

  if (el) {
    observer.observe(el)
  }
})();
