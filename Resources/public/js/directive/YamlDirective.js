
/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('directive.yaml', [])
    .directive('yaml', ['$templateCache', function ($templateCache) {
        return {
            restrict: 'E',
            scope: { yml: "=" },
            replace: true,
            template: $templateCache.get('yaml.textarea'),
            link: function (scope, element, attrs) {
                if (scope.yml !== undefined) {
                    element[0].value = jsyaml.safeDump(scope.yml);
                }

                element.bind('keyup', function ($event) {
                    var yamlStatus = angular.element(document.getElementById('yamlSyntaxStatus'));
                    try {
                        var yamlBox = angular.element(document.getElementById('yaml'));
                        jsyaml.safeLoad(yamlBox[0].value);
                        yamlStatus[0].innerHTML = 'Yaml :: Syntax is correct.';
                        yamlStatus[0].style.color = 'black';
                    } catch (err) {
                        yamlStatus[0].innerHTML = err.message;
                        yamlStatus[0].style.color = 'red';
                    }

                });

                element.bind('keydown', function ($event) {
                    switch ($event.keyCode) {
                        case 9:
                            $event.preventDefault();
                            var val = $event.target.value,
                                start = $event.target.selectionStart,
                                end = $event.target.selectionEnd;

                            $event.target.value = val.substring(0, start) + "    " + val.substring(end);
                            $event.target.selectionStart = $event.target.selectionEnd = start + 2;

                            return false;
                    }

                    return false;
                });
            }
        };
    }]);
