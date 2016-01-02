<?php
/**
 * Provides CMS Administration of Tenon results
 *
 * @package silverstripe-tenon
 * @author josh@novaweb.co.nz
 */
class TenonAdmin extends ModelAdmin
{

    private static $managed_models = array(
        'TenonResult',
    );

    private static $menu_icon = 'tenon/images/tenon_rev.png';
    private static $menu_priority = -0.4;
    private static $menu_title = 'Tenon Results';
    private static $url_segment = 'tenon';

    public function getEditForm($id = null, $fields = null)
    {
        $editForm = parent::getEditForm($id, $fields);

        $roleGridField = $editForm->Fields()->fieldByName('TenonResult');

        if ($roleGridField instanceof GridField) {
            $this->addPaginatorWithShowAll($roleGridField);
        }

        return $editForm;
    }

    private function addPaginatorWithShowAll($gridField)
    {
        $gridField->getConfig()->removeComponentsByType('GridFieldPaginator')->addComponent(new GridFieldPaginatorWithShowAll(30));
    }
}
