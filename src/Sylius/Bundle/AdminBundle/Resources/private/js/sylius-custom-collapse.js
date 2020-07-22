//  ------------------------------------------------------=> CUSTOM COLLAPSE
/**
 * @description Show and hide blocks on click
 *
 * @param {HTMLElement} $blockClick - Element that will be clickable to show or hide
 * @param {HTMLElement} $blockTarget - Target element that will be hidden or displayed
 * @param {HTMLElement} $class - Class that we will put for to hide or display
 * @param {boolean} [collapseAll=true] - Allows to leave open or close by default when clicking on an element.
 * - true = close everything by clicking on an element
 * - false = always leave the elements open
 */
function customCollapse($blockClick, $blockTarget, $class, collapseAll = true) {
  const blockClick = document.querySelectorAll($blockClick);
  blockClick.forEach((item) => {
    item.addEventListener('click', function () {
      // Closing other collapses
      if (collapseAll === true) {
        if (!this.closest($blockTarget).classList.contains($class)) {
          blockClick.forEach((itemClean) => {
            itemClean.closest($blockTarget).classList.remove($class);
          });
        }
      }
      // Opening / Closing of the targeted collapse
      this.closest($blockTarget).classList.toggle($class);
    });
  });
}

export default customCollapse;
