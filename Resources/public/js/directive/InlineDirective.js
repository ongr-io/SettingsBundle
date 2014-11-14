
/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

angular
    .module('directive.inline', [])
    .directive('inline', ['$http', 'asset', function ($http, $asset) {
        return {
            restrict: "A",
            scope: { setting: "="},
            templateUrl: $asset.getLink('template/inline.html'),
            link: function(scope, element, attr) {

                var inputElement = angular.element(element[0].children[1].children[1])[0];
                element.addClass('inline-edit');

                scope.value = scope.setting.data.value;

                /**
                 * appears input field
                 */
                scope.edit = function() {
                    scope.oldValue = scope.value;
                    element.addClass('active');
                    inputElement.focus();
                };

                /**
                 * closes input field
                 */
                scope.close = function() {
                    scope.value = scope.oldValue;
                    element.removeClass('active');
                };

                /**
                 * saves values with ajax request
                 */
                scope.save = function() {
                    element.removeClass('active');

                    requestUrl = Routing.generate('ongr_admin_setting_ng_edit',
                        {
                            name: scope.setting.name,
                            domain: scope.setting.domain
                        }
                    );

                    $http({
                        method:"POST",
                        url: requestUrl,
                        data: {
                            setting: {
                                data: {
                                    value: scope.value
                                }
                            }
                        }
                    });
                };

                /**
                 * Extra shortcuts for better user experience
                 *
                 * @param e Event
                 */
                scope.keyPress = function(e) {
                    switch(e.keyCode) {
                        case 13: //enter
                            scope.save();
                            break;
                        case 27: //esc
                            scope.close();
                            break;
                    }
                }
            }
        }
    }]);
