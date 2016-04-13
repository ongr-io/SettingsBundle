<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class FormValidator
{
    /**
     * Validates add or edit setting form
     *
     * @param Request $request
     *
     * @return array
     */
    public function validateSettingForm(Request $request)
    {
        $parser = new Parser();
        $return = [];
        $return['error'] = '';
        $return['name'] = $request->request->get('settingName');
        $return['type'] = $request->request->get('settingType');
        $return['description'] = $request->request->get('settingDescription');
        $profiles = $request->request->get('settingProfiles');

        is_string($profiles) ? $return['profiles'] = [$profiles] : $return['profiles'] = $profiles;

        if ($return['name'] == '') {
            $return['error'] = 'You must set a name to the setting. ';
        }
        if (count($return['profiles']) == 0) {
            $return['error'] = $return['error'].'At least 1 profile has to be set. ';
        }
        switch ($return['type']) {
            case 'bool':
                $request->request->get('setting-boolean') == 'true' ?
                    $return['value'] = true :
                    $return['value'] = false;
                break;
            case 'string':
                $return['value'] = $request->request->get('setting-default');
                if ($return['value'] == '') {
                    $return['error'] = $return['error'].'You must set a value to the setting. ';
                }
                break;
            case 'object':
                try {
                    $return['value'] = json_encode($parser->parse($request->request->get('setting-object')));
                } catch (\Exception $e) {
                    $return['error'] = $return['error'].'Passed setting value does not contain valid yaml. ';
                }
                if ($return['value'] == '') {
                    $return['error'] = $return['error'].'You must set a value to the setting. ';
                }
                break;
            case 'array':
                $return['value'] = [];
                foreach ($request->request->all() as $key => $item) {
                    if (preg_match('/setting-array_[0-9]*/', $key)) {
                        $return['value'][] = $item;
                    }
                }
                if (count($return['value']) == 0 || $return['value'][0] == '') {
                    $return['error'] = $return['error'].'You must set a value to the setting. ';
                }
                break;
        }

        return $return;
    }

    /**
     * Validates profile form
     *
     * @param Request $request
     * @param array   $profiles
     *
     * @return array
     */
    public function validateProfileForm(Request $request, array $profiles)
    {
        $return = [];
        $return['error'] = '';
        $return['name'] = $request->request->get('profileName');
        $return['description'] = $request->request->get('profileDescription');

        if ($return['name'] == '') {
            $return['error'] = 'You must set a unique name of the profile.';
            return $return;
        }
        foreach ($profiles as $profile) {
            if ($return['name'] == $profile['name']) {
                $return['error'] = 'The profile `'.$profile['name'].'` is already set.';
                return $return;
            }
        }

        return $return;
    }
}
