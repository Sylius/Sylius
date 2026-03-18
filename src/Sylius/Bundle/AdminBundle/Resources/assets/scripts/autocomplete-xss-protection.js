/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

const escapeHtml = (str) => {
    if (typeof str !== 'string') return str;
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
};

document.addEventListener('autocomplete:pre-connect', (event) => {
    const options = event.detail.options;

    if (!options.render) {
        return;
    }

    const labelField = options.labelField || 'text';

    const wrapRenderer = (renderer) => {
        if (!renderer) return renderer;
        return (data, escape) => {
            const escaped = { ...data };
            if (escaped[labelField]) {
                escaped[labelField] = escapeHtml(escaped[labelField]);
            }
            return renderer(escaped, escape);
        };
    };

    if (options.render.item) {
        options.render.item = wrapRenderer(options.render.item);
    }
    if (options.render.option) {
        options.render.option = wrapRenderer(options.render.option);
    }
});
