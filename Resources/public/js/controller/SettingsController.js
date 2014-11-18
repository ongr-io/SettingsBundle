/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('controller.settings', [])
    .controller('settings', ['$scope', '$http', 'DATA', 'domains', '$modal', 'asset', 'settingsList',
        function($scope, $http, DATA, domains, $modal, $asset, settingsList) {

            /**
             * @type {}
             */
            settingsList.list = DATA;

            /**
             * @type {}
             */
            $scope.settings = settingsList.list;

            /**
             * @type {}
             */
            $scope.domains = domains;

            /**
             * Calls a duplicate modal
             *
             * @param settingId string
             */
            $scope.duplicate = function(settingId) {
                var modalInstance = $modal.open({
                    templateUrl: $asset.getLink('template/duplicateModal.html'),
                    controller: 'duplicate',
                    resolve: {
                        item: function() {
                            return {
                                setting: $scope.settings[settingId]
                            };
                        },
                        domains: function() {
                            var domainNames = [];

                            angular.forEach($scope.domains, function(value, key) {
                                this.push(value.value);
                            }, domainNames);

                            return domainNames;
                        }
                    }
                });
            };

            /**
             * Calls new setting modal
             */
            $scope.addSetting = function() {
                var modalInstance = $modal.open({
                    templateUrl: $asset.getLink('template/addSettingModal.html'),
                    controller: 'addSetting',
                    resolve: {
                        domains: function() {
                            var domainNames = [];

                            angular.forEach($scope.domains, function(value, key) {
                                this.push(value.value);
                            }, domainNames);

                            return domainNames;
                        }
                    }
                });
            };

            /**
             * Removes setting through api
             *
             * @param $index string
             * @param setting {{}}
             */
            $scope.remove = function($index, setting) {
                $http({
                    method: "DELETE",
                    url: Routing.generate(
                        'ongr_admin_setting_remove',
                        {
                            name: setting.name,
                            domain: setting.domain,
                        }
                    )
                }).
                    success(function(data, status) {
                        $scope.settings.splice($index, 1);
                    });
            };

            /**
             * Saves bool status
             *
             * @param $event Event
             * @param setting {{}}
             */
            $scope.saveStatus = function($event, setting) {
                var status = $event.target.innerHTML;
                var link = Routing.generate(
                    'ongr_admin_setting_ng_edit',
                    {
                        name: setting.name,
                        domain: setting.domain,
                    }
                );

                if (status == undefined) {
                    return;
                }

                status = status == "On"; //true or false
                $http({method: "POST", url: link, data: {setting: {data: {"value": status}}}});
            };

            /**
             * Generates link for editing
             *
             * @param setting
             *
             * @returns string
             */
            $scope.editLink = function(setting) {
                return Routing.generate(
                    'ongr_admin_setting_edit',
                    {
                        name: setting.name,
                        domain: setting.domain
                    }
                );
            }
        }
    ]);
