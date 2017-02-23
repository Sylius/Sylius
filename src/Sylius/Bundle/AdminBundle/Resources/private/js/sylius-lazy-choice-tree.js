/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    $.fn.extend({
        choiceTree: function (type, multiple, defaultLevel) {
            var tree = $(this);
            var loader = $(this).find('.dimmer');
            var loadedLeafs = [];
            var $input = $(this).find('input[type="hidden"]');

            tree.api({
                on: 'now',
                method: 'GET',
                url: tree.data('taxon-root-nodes-url'),
                cache: false,
                beforeSend: function(settings) {
                    loader.addClass('active');

                    return settings;
                },
                onSuccess: function (response) {
                    var rootContainer = createRootContainer();
                    $.each(response, function (rootNodeIndex, rootNode) {
                        rootContainer.append(createLeaf(rootNode.name, rootNode.code, rootNode.hasChildren, multiple, rootNode.level));
                    });
                    tree.append(rootContainer);
                    loader.removeClass('active');
                }
            });

            var createLeaf = function (name, code, hasChildren, multipleChoice, level) {
                var displayNameElement = createLeafTitleSpan(name);
                var titleElement = createLeafTitleElement();
                var iconElement = createLeafIconElement();
                var checkboxElement = createCheckboxElement(name, code, multipleChoice);

                bindCheckboxAction(checkboxElement);

                var leafElement = $('<div class="item"></div>');
                var leafContentElement = createLeafContentElement();

                leafElement.append(iconElement);
                titleElement.append(displayNameElement);
                titleElement.append(checkboxElement);
                leafContentElement.append(titleElement);

                if (!hasChildren) {
                    iconElement.addClass('outline');
                }
                if (hasChildren) {
                    bindExpandLeafAction(code, displayNameElement, leafContentElement, iconElement, level);
                }
                leafElement.append(leafContentElement);

                return leafElement;
            };

            var createRootContainer = function () {
                return $('<div class="ui list"></div>');
            };

            var createLeafContainerElement = function () {
                return $('<div class="list"></div>');
            };

            var createLeafIconElement = function () {
                return $('<i class="folder icon"></i>');
            };

            var createLeafTitleElement = function () {
                return $('<div class="header"></div>');
            };

            var createLeafTitleSpan = function (displayName) {
                return $('<span style="margin-right: 5px; cursor: pointer;">'+displayName+'</span>')
            };

            var createLeafContentElement = function () {
                return $('<div class="content"></div>');
            };

            var createCheckboxElement = function (name, code, multiple) {
                var chosenNodes = $input.val().split(',');
                var checked = '';
                if (chosenNodes.some(function (chosenCode) {return chosenCode === code})) {
                    checked = 'checked="checked"';
                }
                if (multiple) {
                    return $('<div class="ui checkbox" data-value="'+code+'"><input '+checked+' type="checkbox" name="'+type+'"></div>');
                }

                return $('<div class="ui radio checkbox" data-value="'+code+'"><input '+checked+' type="radio" name="'+type+'"></div>');
            };

            var isLeafLoaded = function (code) {
                return loadedLeafs.some(function (leafCode) {
                    return leafCode === code;
                })
            };

            var bindExpandLeafAction = function (parentCode, expandButton, content, icon, level) {
                var leafContainerElement = createLeafContainerElement();
                if (defaultLevel > level) {
                    loadLeafAction(parentCode, expandButton, content, leafContainerElement);
                    icon.addClass('open');
                }

                expandButton.click(function () {
                    loadLeafAction(parentCode, expandButton, content, leafContainerElement);
                    leafContainerElement.toggle(200, function () {
                        if (icon.hasClass('open')) {
                            icon.removeClass('open');

                            return;
                        }

                        icon.addClass('open');
                    });
                });
            };

            var loadLeafAction = function (parentCode, expandButton, content, leafContainerElement) {
                if (!isLeafLoaded(parentCode)) {
                    expandButton.api({
                        on: 'now',
                        url: tree.data('taxon-leafs-url'),
                        method: 'GET',
                        cache: false,
                        data: {
                            parentCode: parentCode
                        },
                        beforeSend: function(settings) {
                            loader.addClass('active');

                            return settings;
                        },
                        onSuccess: function (response) {
                            $.each(response, function (leafIndex, leafNode) {
                                leafContainerElement.append(createLeaf(leafNode.name, leafNode.code, leafNode.hasChildren, multiple, leafNode.level));
                            });
                            content.append(leafContainerElement);
                            loader.removeClass('active');
                            loadedLeafs.push(parentCode);
                        }
                    });
                }
            };

            var bindCheckboxAction = function (checkboxElement) {
                checkboxElement.checkbox({
                    onChange: function () {
                        var $checkboxes = tree.find('.checkbox');
                        var checkedValues = [];

                        $checkboxes.each(function () {
                            if ($(this).checkbox('is checked')) {
                                checkedValues.push($(this).data('value'));
                            }
                        });

                        $input.val(checkedValues.join());
                    }
                });
            };
        }
    });
})(jQuery);
