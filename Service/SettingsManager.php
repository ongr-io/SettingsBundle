<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Service;

use ONGR\AdminBundle\Event\SettingChangeEvent;
use ONGR\AdminBundle\Model\SettingModel;
use ONGR\DDALBundle\Exception\DocumentNotFoundException;
use ONGR\DDALBundle\Session\SessionModelAwareInterface;
use ONGR\DDALBundle\Session\SessionModelInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SettingsManager implements SessionModelAwareInterface
{
    /**
     * @var SessionModelInterface
     */
    protected $sessionModel;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor
     *
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher)
    {
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns DDAL session model or throws exception if it is not set
     *
     * @return SessionModelInterface
     * @throws \LogicException
     */
    protected function getSessionModel()
    {
        if ($this->sessionModel === null) {
            throw new \LogicException('setSessionModel must be called before getSettings.');
        }

        return $this->sessionModel;
    }

    /**
     * overwrites setting with given name
     *
     * @param string $name
     * @param string|array $value
     * @param string $domain
     *
     * @throws \LogicException
     */
    public function set($name, $value, $domain = 'default')
    {
        switch (gettype($value)) {
            case 'boolean':
                $type = SettingModel::TYPE_BOOLEAN;
                break;
            case 'array':
                $type = SettingModel::TYPE_ARRAY;
                break;
            case 'object':
                $type = SettingModel::TYPE_OBJECT;
                break;
            default:
                $type = SettingModel::TYPE_STRING;
        }

        $settings = [
            'name' => $name,
            'description' => 'ongr_admin.' . $this->translator->trans($name),
            'data' => (object)['value' => $value],
            'type' => $type,
            'domain' => $domain,
        ];

        $model = new SettingModel();
        $model->setDocumentId($domain . '_' . $name);
        $model->assign($settings);

        $this->getSessionModel()->saveDocument($model);
        $this->getSessionModel()->flush();

        $this->eventDispatcher->dispatch('ongr_admin.setting_change', new SettingChangeEvent('save'));
    }

    /**
     * Saves setting.
     *
     * @param SettingModel $model
     */
    public function save(SettingModel $model)
    {
        $this->getSessionModel()->saveDocument($model);
        $this->getSessionModel()->flush();

        $this->eventDispatcher->dispatch('ongr_admin.setting_change', new SettingChangeEvent('save'));
    }

    /**
     * Removes a setting.
     *
     * @param SettingModel $model
     */
    public function remove(SettingModel $model)
    {
        $this->getSessionModel()->deleteDocumentById($model->getDocumentId());
        $this->getSessionModel()->flush();

        $this->eventDispatcher->dispatch('ongr_admin.setting_change', new SettingChangeEvent('delete'));
    }

    /**
     * Copy a setting to the new domain.
     *
     * @param SettingModel $setting
     * @param string       $newDomain
     */
    public function duplicate(SettingModel $setting, $newDomain)
    {
        $newSetting = clone $setting;

        $newSetting->setDocumentId("{$newDomain}_{$setting->name}");
        $newSetting->domain = $newDomain;

        $this->save($newSetting);
    }


    /**
     * {@inheritdoc}
     */
    public function setSessionModel(SessionModelInterface $sessionModel)
    {
        $this->sessionModel = $sessionModel;
    }

    /**
     * Returns setting model by name and domain or creates new if $mustExist is set to FALSE.
     *
     * @param string $name
     * @param string $domain
     * @param bool   $mustExist
     * @param string $type
     *
     * @return SettingModel
     * @throws DocumentNotFoundException
     */
    public function get($name, $domain = 'default', $mustExist = true, $type = 'string')
    {
        try {
            $setting = $this->getSessionModel()->getDocumentById("{$domain}_{$name}");
        } catch (DocumentNotFoundException $exception) {

            if ($mustExist) {
                throw $exception;
            }

            $setting = $this->createSetting($name, $domain, $type);
        }

        return $setting;
    }

    /**
     * Creates new setting object.
     *
     * @param string $name
     * @param string $domain
     * @param string $type
     *
     * @return SettingModel
     */
    protected function createSetting($name, $domain, $type)
    {
        $setting = new SettingModel();
        $setting->setDocumentId("{$domain}_{$name}");
        $setting->name = $name;
        $setting->domain = $domain;
        $setting->type = $type;

        switch ($type) {
            case 'array':
                $setting->data['value'] = [];
                break;
        }

        return $setting;
    }
}
