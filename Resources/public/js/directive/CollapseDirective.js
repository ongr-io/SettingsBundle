/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('directive.collapse', ['filter.objLimitTo'])
    .directive('collapse', ['asset',
        function ($asset) {
            return {
                restrict: 'A',
                scope: {data: "=collapseData"},
                templateUrl: $asset.getLink('template/collapse.html'),
                link: function (scope, element, attr) {

                    /**
                     * Change this to (un)cover more elements
                     *
                     * @type {number}
                     */
                    scope.realLimit = 3;

                    /**
                     * This is used by filters
                     *
                     * @type {number}
                     */
                    scope.limit = scope.realLimit;

                    /**
                     * Tells to view if keys are need to be rendered
                     *
                     * @type {boolean}
                     */
                    scope.array = angular.isArray(scope.data);

                    /**
                     * Length of data
                     *
                     * @type {int}
                     */
                    scope.dataLength = scope.array ? scope.data.length : Object.keys(scope.data).length;

                    /**
                     * Collapses element
                     *
                     * @param $event {Event}
                     */
                    scope.toggleCollapse = function ($event) {
                        if (scope.limit === scope.realLimit) {
                            scope.limit = 999;
                            $event.target.innerHTML = 'less';
                        } else {
                            scope.limit = scope.realLimit;
                            $event.target.innerHTML = 'more';
                        }
                    }
                }
            }
        }]);
