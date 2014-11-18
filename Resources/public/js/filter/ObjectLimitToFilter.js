/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('filter.objLimitTo', [])
    .filter('objLimitTo', [function () {
        return function(object, limit) {
            if(Object.keys(object).length < 1){
                return [];
            }

            var out = new Object,
                count = 0;

            angular.forEach(object, function(value, key) {
                if(count >= limit){
                    return false;
                }
                this[key] = value;
                count++;
            }, out);

            return out;
        };
    }]);
