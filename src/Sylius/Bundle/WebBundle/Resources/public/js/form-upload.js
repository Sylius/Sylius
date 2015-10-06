/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $(document).on('change', 'input[type="file"]', function() {
            var $this = $(this);
            var filenames = []
            var filename, filenameElementId, filenameElement, lastIndex;

            var isMultiple = $this.prop("multiple");
            var fileList = $this.prop("files");

            if (isMultiple && fileList) {
                for(var i = 0; i < fileList.length; i++) {
                    filenames.push(fileList[i].name)
                }
            }
            else {
                filename = $this.val();
                // keep only filename.ext -> remove fakepath / fullpath to file
                lastIndex = filename.lastIndexOf("\\");
                if (lastIndex >= 0) {
                    filename = filename.substring(lastIndex + 1);
                    filenames.push(filename);
                }
            }

            // create html element for outputting the filename string, if not yet present
            filenameElementId = 'filename-' + $this.attr('id');
            filenameElement = $('#'+filenameElementId);

            if (!filenameElement.length) {
                filenameElement = $("<span></span>").attr('id', filenameElementId);
                $this.after(filenameElement);
            }

            var output = filenames.join(', ');
            if (filenames.length > 1)
                output = filenames.length + ': ' + output;

            filenameElement.text(output);
        });
    });
})( jQuery );
