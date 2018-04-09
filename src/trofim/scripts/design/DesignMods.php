<?php
namespace trofim\scripts\design;

use std, gui, trofim;

/**
 * Класс для работы с Design модов.
 * 
 * @author TROFIM
 * @url https://github.com/TROFIM-YT/AddonCraft
 */
class DesignMods 
{

    /**
     * Добавить новый item в список mods.
     * 
     * @param array $objectInfo
     */
    static function addItem (array $objectInfo)
    {
        $GUI = new UXPanel();
        $GUI->classes->add('itemMod-box');
        
        $labelName = new UXLabel($objectInfo['info']['name']);
        if ($objectInfo['mode'] == 'disabled') $labelName->style = '-fx-text-fill: red;';
        $labelName->classes->add('itemMod-name');
        $labelName->wrapText = true;
        
        $labelVersion = new UXLabel($objectInfo['info']['version']);
        $labelVersion->classes->add('itemMod-version');
        
        $vBox = new UXVBox([$labelName, $labelVersion]);
        
        $GUI->add($vBox);
        
        app()->getForm(MainForm)->boxMods->items->add($GUI);
    }
    
    /**
     * Показать информацию о моде.
     * 
     * @param array $objectInfo
     */
    static function showInfo (array $objectInfo)
    {
        (new Thread(function () use ($objectInfo) {
            
            if ($objectInfo['info']['logoFile'] && $objectInfo['path']['logo']) {
                $imageLogo = new UXImageArea(new UXImage($objectInfo['path']['logo']));
                $imageLogo->size = [400, 140];
                $imageLogo->stretch = true;
                $imageLogo->style = '-fx-effect: dropshadow(one-pass-box, rgba(0, 0, 0, 0.9), 10, 0.0, 0, 0);';
                
                $boxLogo = new UXVBox([$imageLogo]);
                $boxLogo->classes->add('infoMod-logo');
                $GUI[] = $boxLogo;
            }
            
            $labelName = new UXLabelEx($objectInfo['info']['name']);
            if ($objectInfo['mode'] == 'disabled') $labelName->style = '-fx-text-fill: red;';
            $labelName->wrapText = true;
            $labelName->id = 'infoMod_name';
            $labelName->classes->add('infoMod-name');
            $GUI[] = $labelName;
            
            $labelVersion = new UXLabel(Language::translate('mainform.mods.info.version') . ' ' . $objectInfo['info']['version']);
            $labelVersion->wrapText = true;
            $labelVersion->classes->add('infoMod-version');
            $GUI[] = $labelVersion;
            
            $labelMCVersion = new UXLabel(Language::translate('mainform.mods.info.mcversion') . ' ' . $objectInfo['info']['mcversion']);
            $labelMCVersion->wrapText = true;
            $labelMCVersion->classes->add('infoMod-mcversion');
            $GUI[] = $labelMCVersion;
            
            $labelModID = new UXLabel(Language::translate('mainform.mods.info.id') . ' ' . $objectInfo['info']['modid']);
            $labelModID->wrapText = true;
            $labelModID->classes->add('infoMod-modid');
            $GUI[] = $labelModID;
            
            if ($objectInfo['info']['authorList']) {
                $labelAuthor = new UXLabelEx(Language::translate('mainform.mods.info.author') . ' ' . implode(', ', $objectInfo['info']['authorList']));
                $labelAuthor->wrapText = true;
                $labelAuthor->classes->add('infoMod-author');
                $GUI[] = $labelAuthor;
            }
            
            if ($objectInfo['info']['url']) {
                $labelTextUrl = new UXLabel('Url: ');
                $labelTextUrl->classes->add('infoMod-textUrl');
                
                $labelURL = new UXHyperlink(parse_url($objectInfo['info']['url'])['host']);
                $labelURL->classes->add('infoMod-url');
                $labelURL->tooltipText = $objectInfo['info']['url'];
                $labelURL->tooltip->style = "-fx-font-size: 12px; -fx-font-family: 'System';";
                $labelURL->on('action', function () use ($objectInfo) {
                    $alert = new UXAlert('INFORMATION');
                    $alert->title = app()->getName();
                    $alert->headerText = Language::translate('mainform.message.mods.url.header');
                    $alert->contentText = Language::translate('mainform.message.mods.url.content');
                    $alert->setButtonTypes([Language::translate('word.yes'), Language::translate('word.copy'), Language::translate('word.no')]);
                    $alert->graphic = new UXImageView(new UXImage('res://.data/img/icon/link_alert-24.png'));
                    
                    $textUrl = new UXLabelEx($objectInfo['info']['url']);
                    $textUrl->style = '-fx-font-family: "System"; -fx-font-size: 14px; -fx-text-alignment: CENTER; -fx-alignment: CENTER; -fx-padding: 0 0 7 0;';
                    
                    $textWarning = new UXLabelEx(Language::translate('mainform.message.mods.url.content.label.warning'));
                    $textWarning->style = '-fx-font-family: "Minecraft Rus"; -fx-font-size: 12px; -fx-text-fill: red; -fx-text-alignment: CENTER; -fx-alignment: CENTER;';
                    
                    $box = new UXVBox([$textUrl, $textWarning]);
                    $box->style = '-fx-alignment: CENTER;';
                    
                    $alert->expandableContent = $box;
                    $alert->expanded = true;
                    
                    switch ($alert->showAndWait()) {
                        case Language::translate('word.yes'):
                            open($objectInfo['info']['url']);
                        break;
                        case Language::translate('word.copy'):
                            UXClipboard::setText($objectInfo['info']['url']);
                        break;
                    }
                });
                
                $boxURL = new UXHBox([$labelTextUrl, $labelURL]);
                $GUI[] = $boxURL;
            }
            
            if ($objectInfo['info']['description']) {
                $labelDescription = new UXLabelEx($objectInfo['info']['description']);
                $labelDescription->wrapText = true;
                $labelDescription->classes->add('infoMod-description');
                $GUI[] = $labelDescription;
            }
            
            $mode = $objectInfo['mode'];
            
            uiLater(function () use ($GUI, $mode) {
                if ($mode == 'enabled') app()->getForm(MainForm)->setModeMod(true);
                else if ($mode == 'disabled') app()->getForm(MainForm)->setModeMod(false);
                foreach ($GUI as $item) 
                    app()->getForm(MainForm)->boxInfoMod->items->add($item);
            });
        }))->start();
    }
    
}