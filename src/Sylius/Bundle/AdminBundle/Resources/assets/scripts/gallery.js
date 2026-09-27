import Viewer from 'viewerjs';
import 'viewerjs/dist/viewer.css';

const initGalleries = () => {
    document.querySelectorAll('.spotlight-group').forEach((group) => {
        if (group.querySelector('a.spotlight') === null) {
            return;
        }

        new Viewer(group, {
            filter: (image) => image.closest('a.spotlight') !== null,
            url: (image) => image.closest('a.spotlight').getAttribute('href'),
            title: false,
            rotatable: false,
            scalable: false
        });

        group.addEventListener('click', (event) => {
            if (event.target.closest('a.spotlight')) {
                event.preventDefault();
            }
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGalleries);
} else {
    initGalleries();
}
