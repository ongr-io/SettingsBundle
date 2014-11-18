/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
                requestUrl = Routing.generate('ongr_admin_setting_copy', {
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
