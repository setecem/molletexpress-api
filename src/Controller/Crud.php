<?php

namespace App\Controller;

use Cavesman\Http;
use Cavesman\Modules;
use Cavesman\DB;
use Cavesman\Smarty;

class Crud
{
    public static function __install() {

    }

    public static function menu(): array
    {
        return array(
            "order" => 1000,
            "items" => array(
                array(
                    "order" => 1000,
                    "name" => "tools",
                    "label" => self::trans("Tools"),
                    "icon" => "fa fa-cog",
                    "link" => "#",
                    "permission" => [
                        "action" => "view_tools",
                        "group" => "ACCESS_PERMISSION",
                    ],
                    "childs" => array(
                        "order" => 1000,
                        "items" => array(
                            array(
                                "name" => "crud",
                                "label" => self::trans("Crud"),
                                "icon" => "fa fa-stream",
                                "link" => '/'."crud",
                                "permission" => [
                                    "action" => "view_crud",
                                    "group" => "ACCESS_PERMISSION",
                                ],
                                "childs" => false
                            )
                        )
                    )
                )
            )
        );
    }

    public static function render(): void
    {
        Smarty::set("page", "crud");
        Smarty::set("module", "crud");
        Smarty::set("module_dir", dirname(__FILE__));

        $entitiesClassNames = DB::getManager()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        Http::response(Smarty::partial(dirname(__FILE__)."/tpl/crud.tpl", [
            "entities" => $entitiesClassNames,
            "modules" => Modules::$list
        ]), 200, 'text/html');
    }
    public static function getEntityColumns(): void
    {
        Http::jsonResponse(DB::getManager()->getClassMetadata(self::p("entity"))->getColumnNames());
    }
    public static function save(){

        // TODO: VERIFICAR SI EL DIRECTORIO TIENE PERMISOS DE ESCRITURA is_writable

        //Definimos el directorio del módulo

        if(!self::p("module", false)){
            $module_dir = _MODULES_."/".mb_strtolower(self::p("name"));
            echo "GENERATE DIRS\n";
            self::generateDirectories($module_dir);
            echo "GENERATE CONFIG\n";
            self::generateConfig($module_dir);
            echo "GENERATE MODULE\n";
            self::generateModuleFile($module_dir);
            echo "GENERATE MAIN\n";
            self::generateMainTemplateFile($module_dir);
            echo "GENERATE FORM FILE\n";
            self::generateFormTemplateFile($module_dir);
        }else{
            $module_dir = _MODULES_."/".self::p("module");
        }

        echo "GENERATE ENTITIES\n";
        $fields = '';
        foreach(self::p("fields") as $field)
            $fields .= self::formatField($field);

        $text = self::formatFields(self::p("name"), self::p("entity"), $fields);

        $entity = self::p("entity");

        $array = array(
            "%NAME%" => self::p("module") ? self::p("module") : mb_strtolower(self::p("name")),
            "%TABLENAME%" => mb_strtolower(str_replace(" ", "_", self::p("name"))),
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%ENTITY%" => $entity,
            "%CLASSNAME%" => ucfirst(self::p("module") ? self::p("module") : mb_strtolower(self::p("name")))
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);
        $fp = fopen($module_dir."/entity/".$entity.".php", "w+");
        fwrite($fp, $text);
        fclose($fp);
        echo "EJECUTAMOS DOCTRINE";
        system("cd "._ROOT_." && "._ROOT_."/bin/doctrine orm:schema-tool:update --force");
        system("cd "._ROOT_." && "._ROOT_."/bin/doctrine orm:generate-entities --no-backup "._ROOT_);

        echo "ALL OK\n";

        self::response("OK");

    }
    private static function generateDirectories($module_dir): void
    {
        if(!is_dir($module_dir))
            mkdir($module_dir);

        // Comprobamos si existe el directorio entity
        if(!is_dir($module_dir."/entity"))
            mkdir($module_dir."/entity");

        // Comprobamos si existe el directorio tpl
        if(!is_dir($module_dir."/tpl"))
            mkdir($module_dir."/tpl");

        // Comprobamos si existe el directorio tpl/form
        if(!is_dir($module_dir."/tpl/form"))
            mkdir($module_dir."/tpl/form");
    }
    private static function generateConfig($module_dir): array|bool|string
    {
        $text = file_get_contents(dirname(__FILE__)."/template/config.template");
        $array = array(
            "%NAME%" => mb_strtolower(self::p("name")),
            "%MODULE%" => mb_strtolower(self::p("name"))
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);
        $fp = fopen($module_dir."/config.json", "w+");
        fwrite($fp, $text);
        fclose($fp);
        return $text;
    }
    private static function parseName(string $name = ''): array|string
    {
        $name = ucwords(str_replace("_", " ", $name));
        return str_replace(" ", "", $name);

    }
    private static function generateModuleSetters($field): array|bool|string
    {
        $text = file_get_contents(dirname(__FILE__)."/template/module-setters.template");
        $array = array(
            "%NAME%" => $field['name'],
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%UNAME%" => self::parseName($field['name']),
            "%TYPE%" => $field['type'],
            "%DEFAULT%" => (string)$field['default'] != "NULL" ? $field['default'] : NULL,
            "%NULLABLE%" => (string)$field['default'] != "NULL" ? "false" : "true"
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);

        return $text;
    }
    private static function generateModuleFile($module_dir): void
    {
        $text = file_get_contents(dirname(__FILE__)."/template/module.template");

        $fields = '';
        foreach(self::p("fields") as $field)
            $fields .= self::generateModuleSetters($field);

        $array = array(
            "%NAME%" => mb_strtolower(self::p("name")),
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%UNAME%" => self::parseName($field['name']),
            "%ENTITY%" => ucfirst(self::p("entity")),
            "%CLASSNAME%" => ucfirst(self::p("name")),
            "%FIELDS%" => $fields,
            "%ICON%" => self::p("icon")
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);
        $fp = fopen($module_dir."/".mb_strtolower(self::p("name")).".php", "w+");
        fwrite($fp, $text);
        fclose($fp);
    }
    private static function generateFormField($field): array|bool|string
    {
        $text = file_get_contents(dirname(__FILE__)."/template/theme-form-field.template");
        switch($field['type']){
            case 'string':
            case 'text':
                $type = "text";
                break;
            case 'boolean':
                $type = "checkbox";
                break;
            case 'decimal':
                $type = "number";
            default:
                $type = 'text';
        }
        $array = array(
            "%NAME%" => $field['name'],
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%UNAME%" => self::parseName($field['name']),
            "%TYPE%" => $type,
            "%DEFAULT%" => (string)$field['default'] != "NULL" ? $field['default'] : "NULL",
            "%NULLABLE%" => (string)$field['default'] != "NULL" ? "false" : "true"
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);

        return $text;
    }
    private static function generateFormTemplateFile($module_dir): void
    {
        $text = file_get_contents(dirname(__FILE__)."/template/theme-form.template");
        $fields = '';
        foreach(self::p("fields") as $field)
            if(isset($field['form']) && $field['form'])
                $fields .= self::generateFormField($field);

        $array = array(
            "%NAME%" => mb_strtolower(self::p("name")),
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%ENTITY%" => ucfirst(self::p("entity")),
            "%CLASSNAME%" => ucfirst(self::p("name")),
            "%FIELDS%" => $fields
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);


        $fp = fopen($module_dir."/tpl/form/".mb_strtolower(self::p("name")).".tpl", "w+");
        fwrite($fp, $text);
        fclose($fp);
    }
    private static function generateMainField($field, $header = false): array|bool|string
    {
        if($header)
            $text = file_get_contents(dirname(__FILE__)."/template/theme-main-field-header.template");
        else
            $text = file_get_contents(dirname(__FILE__)."/template/theme-main-field.template");

        $array = array(
            "%NAME%" => $field['name'],
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%UNAME%" => self::parseName($field['name'])
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);

        return $text;
    }
    private static function generateMainTemplateFile($module_dir): void
    {
        $text = file_get_contents(dirname(__FILE__)."/template/theme-main.template");

        $fields = '';
        foreach(self::p("fields") as $field)
            if(isset($field['show']) && $field['show'])
                $fields .= self::generateMainField($field);
        $fields_h = '';
        foreach(self::p("fields") as $field)
            if(isset($field['show']) && $field['show'])
                $fields_h .= self::generateMainField($field, true);
        $array = array(
            "%NAME%" => mb_strtolower(self::p("name")),
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%CLASSNAME%" => ucfirst(self::p("name")),
            "%FIELDS%" => $fields,
            "%FIELDS_HEADERS%" => $fields_h
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);
        $fp = fopen($module_dir."/tpl/".mb_strtolower(self::p("name")).".tpl", "w+");
        fwrite($fp, $text);
        fclose($fp);
    }

    private static function formatField(array $field): array|bool|string
    {
        if($field['foreignkey'])
            $text = file_get_contents(dirname(__FILE__)."/template/field-entity-foreign.template");
        else
            $text = file_get_contents(dirname(__FILE__)."/template/field-entity.template");
        $array = array(
            "%NAME%" => $field['name'],
            "%FOREIGN%" => $field['foreignkey'],
            "%FOREIGNFIELD%" => $field['foreignkeyfield'],
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%TYPE%" => $field['type'],
            "%DEFAULT%" => (string)$field['default'] != "NULL" ? $field['default'] : "NULL",
            "%NULLABLE%" => (string)$field['default'] != "NULL" ? "false" : "true"
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);

        return $text;
    }

    private static function formatGetterField(array $field): array|bool|string
    {
        $text = file_get_contents(dirname(__FILE__)."/template/getters.template");
        $array = array(
            "%NAME%" => $field['name'],
            "%MODULE%" => mb_strtolower(self::p("name")),
            "%UNAME%" => self::parseName($field['name']),
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);

        return $text;
    }

    private static function formatFields(string $name, string $entity, string $fields): array|bool|string
    {
        $text = file_get_contents(dirname(__FILE__)."/template/entity.template");
        $array = array(
            "%NAME%" => $name,
            "%MODULE%" => self::p("module") ? self::p("module") : mb_strtolower(self::p("name")),
            "%ENTITY%" => $entity,
            "%FIELDS%" => $fields
        );
        foreach($array as $find => $replace)
            $text = str_replace($find, $replace, $text);

        return $text;
    }

}