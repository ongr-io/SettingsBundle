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
    .module('controller.modal.duplicate', [])
    .controller('duplicate', ['$scope', '$modalInstance', '$http', 'item', 'domains',
        function($scope, $modalInstance, $http, item, domains) {

            /**
             * @type {}
             */
            $scope.setting = item.setting;

            /**
             * @type string
             */
            $scope.newDomain = 'default';

            /**
             * @type []
             */
            $scope.domains = domains;

            /**
             * Copies setting to another domain
             *
             * @param newDomain string
             */
            $scope.copy = function(newDomain) {
                requestUrl = Routing.generate('fox_admin_setting_copy', {
                    name: $scope.setting.name,
                    from: $scope.setting.domain,
                    to: newDomain
                });

                $http({ method: "GET", url: requestUrl  })
                    .success(function(data, status) {
                        $modalInstance.close();
                        window.location.reload();
                    });
            };

            /**
             * closes modal
             */
            $scope.cancel = function() {
                $modalInstance.dismiss('cancel');
            };
        }
    ]);
