<?

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @property  arParams
 * @property  arResult
 */
class IblockList extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams): array
    {
        if (!isset($arParams["CACHE_TIME"])) {
            $arParams["CACHE_TIME"] = 86000;
        }

        $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

        if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER"])) {
            $arParams["SORT_ORDER"] = "DESC";
        }

        if (!is_array($arParams["FIELD_CODE"])) {
            $arParams["FIELD_CODE"] = [];
        }

        foreach ($arParams["FIELD_CODE"] as $key => $val) {
            if (!$val) {
                unset($arParams["FIELD_CODE"][$key]);
            }
        }

        if (!is_array($arParams["PROPERTY_STRING_ID"])) {
            $arParams["PROPERTY_STRING_ID"] = [];
        }

        foreach ($arParams["PROPERTY_STRING_ID"] as $key => $val) {
            if (!$val) {
                unset($arParams["PROPERTY_STRING_ID"][$key]);
            }
        }

        if (!is_array($arParams["PROPERTY_INTEGER_ID"])) {
            $arParams["PROPERTY_INTEGER_ID"] = [];
        }

        foreach ($arParams["PROPERTY_INTEGER_ID"] as $key => $val) {
            if (!$val) {
                unset($arParams["PROPERTY_INTEGER_ID"][$key]);
            }
        }

        if (!is_numeric($arParams["ELEMENT_COUNT"]) || !$arParams["ELEMENT_COUNT"] > 0) {
            $arParams["ELEMENT_COUNT"] = 10;
        }

        foreach ($arParams["PROPERTY_CODE"] as $key => $val) {
            if ($val === "") {
                unset($arParams["PROPERTY_CODE"][$key]);
            }
        }

        return $arParams;
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль iblock не установлен.');
        }
    }

    protected function getResult()
    {
        $select = $this->arParams['FIELD_CODE'];
        $runtime = [];

        if (!empty($this->arParams["PROPERTY_SINGLE_ID"])) {
            $arSingleProp = [];

            foreach ($this->arParams["PROPERTY_SINGLE_ID"] as $prop_id) {
                $arSingleProp['PROPERTY_' . $prop_id] = ['data_type' => 'string'];
            }

            $entityPropsSingle = Bitrix\Main\Entity\Base::compileEntity(
                sprintf('PROPS_SINGLE_%s', $this->arParams['IBLOCK_ID']),
                array_merge(['IBLOCK_ELEMENT_ID' => ['data_type' => 'integer']], $arSingleProp),
                [
                    'table_name' => sprintf('b_iblock_element_prop_s%s', $this->arParams['IBLOCK_ID']),
                ]
            );

            $runtime = [
                'PROPERTY_SINGLE' => [
                    'data_type' => $entityPropsSingle->getDataClass(),
                    'reference' => [
                        '=this.ID' => 'ref.IBLOCK_ELEMENT_ID'
                    ],
                    'join_type' => 'inner'
                ],
            ];

            $select = array_merge($select, ['PROPERTY_SINGLE.*']);
        }

        $dbItems = \Bitrix\Iblock\ElementTable::getList([
            'order'         => [$this->arParams['SORT_BY'] => $this->arParams['SORT_ORDER']],
            'select'        => $select,
            'filter'        => ['IBLOCK_ID' => $this->arParams['IBLOCK_ID']],
            'group'         => [],
            'limit'         => $this->arParams['ELEMENT_COUNT'],
            'offset'        => 0,
            'count_total'   => 1,
            'runtime'       => $runtime,
            'data_doubling' => false,
            'cache'         => [
                'ttl'         => $this->arParams['CACHE_TIME'],
                'cache_joins' => true
            ],
        ]);

        $this->arResult = $dbItems->fetchAll();
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
            $this->includeComponentTemplate();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }
}
