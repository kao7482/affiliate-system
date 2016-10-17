<?php


/**
 * Class Model_Base
 * [BACKEND]DB基礎クラス
 *
 * ORM　MODEL_SOFTは論理削除モデルです。
 * 削除した場合、deleted_at にtimestampが保存されます。
 *
 * @package \Orm\Model_Soft
 */
abstract class Model_Base extends \Orm\Model_Soft
{


    /**
     * テーブル名
     *
     * @var string
     */
    protected static $_table_name = null;


    /**
     * リレーション(belongs_to)の親キー
     * リレーションがある場合、親モデルに対してのキーを設定する。
     * が、これは旧論理。特に必要ない。
     * ※もともと\Orm\Model_Softにこのようなプロパティは存在しない。
     * @var null
     */
    protected static $_parent_key = null;


    /**
     * DBのPKを定義する。
     * ※もともと\Orm\Model_のプロパティ？。
     *
     * @var array
     */
    protected static $_primary_key = ['id'];


    /**
     * GETTER
     *
     * @param $property
     * @return mixed
     */
    public function & __get($property)
    {
        return parent::__get($property);
    }


    /**
     * ORM　RELATION　プロパティ
     *　belongs_to
     *
     * @var array
     */
    protected static $_belongs_to = [];


    /**
     * ORM　RELATION　プロパティ
     * has_one
     *
     * @var array
     */
    protected static $_has_one = [];


    /**
     * ORM　RELATION　プロパティ
     * has_many
     *
     * @var array
     */
    protected static $_has_many = [];


    /**
     * ORM　RELATION　プロパティ
     * many_many
     *
     * @var array
     */
    protected static $_many_many = [];


    /**
     * ORM EAVコンテナー
     *
     * @var array
     */
    protected static $_eav = [];


    /**
     * テーブルプロパティ
     *
     * @var array
     */
    protected static $_properties = [];


    /**
     * OWNER　プロパティ | 独自実装
     * 現状使用していません。
     *
     * @var null
     */
    protected static $_owner = null;


    /**
     * ヒント用ツールチップを出力
     *
     * @param null $property フィールド名
     * @param null $namespace　名前空間（モデル名）例） Model_Userの場合 => user
     * @return string
     */
    public static function getHintToolTip($property = null, $namespace = null)
    {
        $namespace or $namespace = static::getNamespace();
        $hint_text = __($namespace . '.hint.' . \Str::lower($property), [], \Str::lower($property));
        $attribute = [
            'class'          => 'form-hint',
            'data-toggle'    => 'tooltip',
            'data-placement' => 'top',
            'data-title'     => $hint_text,
            'title'          => $hint_text,
        ];

        return html_tag('a', $attribute, html_tag('i', ['class' => 'fa fa-question-circle']));
    }


    /**
     * ラベルを出力
     *
     * @param string $key　KEYでSPANタグのクラスが変動する。
     * @param null $content　HTMLコンテンツまたは文字列
     * @return string
     */
    public static function getLabel($key = 'active', $content = null)
    {
        $class = null;
        switch ($key)
        {
            case 'active' :
                $class = 'fa fa-check text-success';

                break;

            case 'inactive' :
                $class = 'fa fa-remove text-danger';

                break;

            default :
                return null;

                break;
        }

        return html_tag('span', ['class' => $class]);
    }


    /**
     * アイコンを出力
     *
     * @param string $key
     * @return null|string
     */
    public static function getIcon($key = 'active')
    {
        $key and $key = \Str::lower($key);
        $class = null;
        switch ($key)
        {
            case 'active' :
                $class = 'fa fa-check text-success';

                break;

            case 'inactive' :
                $class = 'fa fa-remove text-danger';

                break;

            default :
                return null;

                break;
        }

        return html_tag('i', ['class' => $class]);
    }


    /**
     * SUCCESS用のアイコンを出力
     * @return string
     */
    public static function successIcon()
    {
        return static::getIcon('active');
    }


    /**
     * エラー用のアイコンを出力
     * @return string
     */
    public static function errorIcon()
    {
        return static::getIcon('inactive');
    }


    /**
     * 論理削除Column　\Orm\Model_Softのプロパティ
     * @var array
     */
    protected static $_soft_delete = [
        'deleted_field'   => 'deleted_at',
        'mysql_timestamp' => false
    ];


    /**
     * observerイベントプロパティ \Orm\Model_Softのプロパティ
     * ここで指定したイベントに対して、自動的に処理を実行することができる。
     *
     * @var array
     */
    protected static $_observers = [
        '\Orm\Observer_Self'      => [
            'events' => [
                'before_insert',
                'before_save',
                'after_save',
                'after_load'
            ]
        ],
        '\Orm\Observer_Typing'    => [
            'events' => [
                'before_save',
                'after_save',
                'after_load'
            ]
        ],
        'Orm\Observer_Validation' => [
            'events' => [
                'before_insert',
                'before_save'
            ],
        ],
        '\Orm\Observer_CreatedAt' => [
            'events'          => [
                'before_insert'
            ],
            'mysql_timestamp' => false,
        ],

        '\Orm\Observer_UpdatedAt' => [
            'events'          => [
                'before_save'
            ],
            'mysql_timestamp' => false,
        ],
    ];


    /**
     *　observerイベント
     * INSERT処理の前に行いたい処理
     *
     */
    public function _event_before_insert()
    {

    }


    /**
     *　observerイベント
     * SAVE処理の前に行いたい処理
     *
     */
    public function _event_before_save()
    {

    }


    /**
     *　observerイベント
     * SAVE処理の後に行いたい処理
     *
     */
    public function _event_after_save()
    {

    }


    /**
     *　observerイベント
     * 　LOAD後のイベントに合わせて行いたい処理。
     *
     */
    public function _event_after_load()
    {
        $this->created_at = $this->timeConvertByVal($this->created_at);
        $this->updated_at = $this->timeConvertByVal($this->updated_at);
        $this->deleted_at = $this->timeConvertByVal($this->deleted_at);
    }


    /**
     * モデルオブジェクトが新規作成されたかどうか。
     * @return bool
     */
    public function is_new()
    {
        return $this->_is_new;
    }


    /**
     * 最初に呼ばれる処理
     * ここで指定した何かの処理をMODELオブジェクト呼び出し時に必ず実行する。
     * たとえば、$_propertiesに設定した項目を動的に変化させたい場合、
     * ここで変更が可能。
     * 現状は特に利用していない。
     */
    public static function _init()
    {

    }


    /**
     * テーブル名から単数形を取得する。
     * 例）users => user
     *
     * @return mixed
     */
    public static function getNamespace()
    {
        return str_replace('_', null, \Inflector::singularize(static::$_table_name));
    }


    /**
     * リレーションを取得する。
     * 現状特に利用していない。
     *
     * @param array $has_relation
     * @return null
     */
    protected static function getRelations($has_relation = [])
    {
        $name = $has_relation['name'];
        $type = $has_relation['type'];
        $condition = $has_relation['condition'];
        $relation = null;
        $relation_model = null;
        switch ($type)
        {
            case '_belongs_to':
                if (array_key_exists($name, static::$_belongs_to))
                {
                    if (array_key_exists('where', $condition))
                    {
                        $relation = static::$_belongs_to[$name];
                        $relation_model = $relation['model_to']::query()->where($condition['where']);
                    }
                }

                break;

            case '_has_one':
                //TODO AB

                break;

            case '_has_many':
                //TODO AB

                break;

            case '_many_many':
                //TODO AB

                break;

            default:

                break;
        }

        return $relation_model;
    }


    /**
     * static::$_properties　に動的な値をセットする場合（たとえば__('asianbet.yes'))この関数を利用します。
     * もともとこのシステムは最小のPHPのソースで　新規作成/編集 | 一覧　|　削除などのCRUD画面が
     * 動的に生成できるよう（検索、AJAXのリロード、複雑なJS　VALIDATIONもすべて）に設計されており、多少修正すれば、その機能は今でも動きます。
     * しかし、現在はHTML5 kendo.ui　ベースのUIで実装することになったため、
     * これらの機能は利用していません。
     * 現状特に利用していない。
     *
     * @param string $form_type
     */
    public static function setFormProperties($form_type = 'index')
    {
        $namespace = static::getNamespace();
        foreach (array_keys(static::getProperties()) as $property)
        {
            if (isset(static::$_properties[\Str::lower($property)]))
            {
                $hint_tooltip = ($form_type !== 'index') ? static::getHintToolTip($property) : null;
                static::$_properties[\Str::lower($property)]['label'] = __($namespace . '.label.' . \Str::lower($property), [], \Str::lower($property)) . $hint_tooltip;
                static::$_properties[\Str::lower($property)]['form']['placeholder'] = __($namespace . '.placeholder.' . \Str::lower($property), [], \Str::lower($property));

                if (isset(static::$_properties[\Str::lower($property)]['data_type']) && static::$_properties[\Str::lower($property)]['data_type'] === 'enum')
                {
                    if (isset(static::$_properties[\Str::lower($property)]['form']) && isset(static::$_properties[\Str::lower($property)]['form']['options']))
                    {
                        $options = [];
                        if ($form_type === 'index')
                        {
                            $options[null] = __('asianbet.all', [], 'all');
                        }

                        foreach (static::$_properties[\Str::lower($property)]['form']['options'] as $key => $val)
                        {
                            $options[$key] = __($namespace . '.options.' . \Str::lower($property) . '.' . $key, [], $key);
                        }

                        static::$_properties[\Str::lower($property)]['form']['options'] = $options;
                    }
                }

                if (isset(static::$_properties[\Str::lower($property)]['data_type'])
                    && static::$_properties[\Str::lower($property)]['data_type'] === 'bigint'
                )
                {
                    if (isset(static::$_properties[\Str::lower($property)]['has_relation'])
                        && isset(static::$_properties[\Str::lower($property)]['has_relation']['name'])
                        && isset(static::$_properties[\Str::lower($property)]['has_relation']['type'])
                        && isset(static::$_properties[\Str::lower($property)]['has_relation']['condition'])
                        && isset(static::$_properties[\Str::lower($property)]['has_relation']['display_value'])
                    )
                    {
                        $options = [];
                        if ($form_type === 'index')
                        {
                            $options[null] = __('asianbet.msg.please_select', [], '');
                        }

                        $relation_model = static::getRelations(static::$_properties[\Str::lower($property)]['has_relation']);
                        if ($relation_model)
                        {
                            foreach ($relation_model->get() as $item)
                            {
                                $options[$item->id] = $item->{static::$_properties[\Str::lower($property)]['has_relation']['display_value']};
                            }

                            static::$_properties[\Str::lower($property)]['form']['options'] = $options;
                        }
                    }
                }
            }
        }
    }


    /**
     * テーブル名を単数形で取得する。
     *
     * @return mixed
     */
    public static function getSingularizeTable()
    {
        return \Inflector::singularize(static::$_table_name);
    }


    /**
     * プロパティを取得する。
     *
     * @param null $key KEYを指定した場合、特定のKEYだけ返します。
     * @return array
     */
    public static function getProperties($key = null)
    {
        return $key && array_key_exists($key, static::$_properties) ? static::$_properties[$key] : static::$_properties;
    }


    /**
     * $_propertiesに設定された初期値を取得します。
     *
     * @param null $parent_key
     * @return array
     */
    public static function getDefaultValue($parent_key = null)
    {
        $props = static::getProperties($parent_key);

        return array_key_exists('default', $props) ? $props['default'] : null;
    }


    /**
     * $_propertiesに値をSETする
     * 現状特に利用はしていない。
     *
     * @param null $key
     * @param array $value
     * @return bool
     */
    public static function setProperties($key = null, $value = [])
    {
        if ($key)
        {
            if (isset(static::$_properties[$key]) && is_array($value))
            {
                static::$_properties[$key] = $value;

                return true;
            }
        }

        return false;
    }


    /**
     * クラス名を取得する。
     *
     * @return string
     */
    protected static function getClass()
    {
        return __CLASS__;
    }


    /**
     * タイムゾーン理論を利用する場合、
     * タイムゾーンの時差を考慮し動的に時間（timestamp）を返す。
     *
     * @param null $val
     * @return null
     */
    protected function timeConvertByVal($val = null)
    {
        if (empty($val))
        {
            return null;
        }

        $timezone = \Utility\ABHelper::getCurrentTimezone();
        $offset = \Utility\ABHelper::getTimezoneOffset($val, $timezone);
        $timestamp = $val + $offset;

        return $timestamp;
    }


    /**
     * ソートできるフィールドを返す。
     * 現状特に利用はしていない。
     *
     * @return array|null
     */
    public static function getSortableField()
    {
        $sortable_fields = [];
        $_properties = static::getProperties();
        foreach (array_keys($_properties) as $_property)
        {
            if (isset(static::$_properties[$_property]) && isset(static::$_properties[$_property]['sort']) && static::$_properties[$_property]['sort'])
            {
                $sortable_fields[] = $_property;
            }
        }

        return $sortable_fields;
    }


    /**
     * 検索のプロパティを取得する
     * 現状特に利用はしていない。
     *
     * @return array
     */
    public static function getSearchConfig()
    {
        $search_config = [];
        $_properties = static::getProperties();
        foreach (array_keys($_properties) as $_property)
        {
            if (isset(static::$_properties[$_property]) && isset(static::$_properties[$_property]['search']) && static::$_properties[$_property]['search'])
            {
                $search_config[$_property] = [
                    'value'   => \Input::post($_property, null),
                    'type'    => static::$_properties[$_property]['form']['type'],
                    'options' => isset(static::$_properties[$_property]['form']['options']) ? static::$_properties[$_property]['form']['options'] : null,
                    'label'   => static::$_properties[$_property]['label']
                ];
            }
        }

        return $search_config;
    }


    /**
     *  $_propertiesに設定された条件から、自動的に検索用のWHEREを生成する
     *　現状特に利用はしていない。
     *
     * @param array $additional_condition
     * @return array
     */
    public static function getQueryCondition($additional_condition = [])
    {
        $where = [];
        $where[] = ['id', '!=', 0];
        $search_config = static::getSearchConfig();
        foreach ($search_config as $key => $val)
        {
            $query_value = \Input::post($key, null);
            if (!$query_value)
            {
                continue;
            }

            switch ($val['type'])
            {
                case 'radio':
                case 'checkbox':
                case 'hidden':
                case 'select':
                    $where[] = [$key, '=', $query_value];
                    break;

                case 'text':
                    $where[] = [$key, 'LIKE', '%' . $query_value . '%'];

                    break;

                default :

                    break;
            }
        }

        if (!empty($additional_condition) && is_array($additional_condition))
        {
            $where = array_merge($where, $additional_condition);
        }

        return $where;
    }


    /**
     * $_propertiesに設定された条件から、フォームのフィールドを生成する。
     * 現状特に利用はしていない。
     *
     * @param string $form_type
     * @param array $exclude
     * @param array $additional
     * @param null $prefix
     * @return array
     */
    public static function getFormField($form_type = 'index', $exclude = [], $additional = [], $prefix = null)
    {
        $form = [];
        static::setFormProperties($form_type);
        $_properties = static::getProperties();
        foreach (array_keys($_properties) as $_property)
        {
            $name = $prefix . $_property;
            switch (\Str::lower($form_type))
            {
                case 'index' :
                    $condition = (isset(static::$_properties[$_property])
                        && isset(static::$_properties[$_property]['label'])
                        && isset(static::$_properties[$_property]['table'])
                        && static::$_properties[$_property]['table']);

                    break;

                default :
                case 'create' :
                case 'update' :
                    $condition = (isset(static::$_properties[$_property])
                        && isset(static::$_properties[$_property]['label'])
                        && isset(static::$_properties[$_property]['form'])
                        && isset(static::$_properties[$_property]['form']['type'])
                        && static::$_properties[$_property]['form']['type']
                    );

                    break;

                case null :
                    $condition = false;

                    break;
            }

            if ($condition)
            {
                if (false === in_array($_property, $exclude))
                {
                    $form[$name] = static::$_properties[$_property];
                }
            }
            unset($name, $_property);
        }
        unset($_properties);

        foreach (array_keys($additional) as $_property)
        {
            $name = $prefix . $_property;
            $form[$name] = $additional[$_property];
            unset($name, $_property);
        }
        unset($additional);

        return $form;
    }


    /**
     * STATUSのoptionに対して動的に言語をセットする。
     * 現状特に利用はしていない。
     *
     * @param string $type
     * @param null $key
     * @return array|null
     */
    public static function getStatusArray($type = 'array', $key = null)
    {
        if (empty(static::$_properties['status']) || empty(static::$_properties['status']['options']))
        {
            return null;
        }

        $model_name = \Inflector::singularize(static::$_table_name);
        $options = static::$_properties['status']['options'];
        $array = [];

        switch ($type)
        {
            case 'array':
                foreach ($options as $option)
                {
                    $array[$option] = $option;
                }

                break;

            case 'select':
            case 'label':
            case 'icon':
                foreach ($options as $option)
                {
                    $array[$option] = __($model_name . '.options.status.' . $type . '.' . $option, [], $options);
                }

                break;

            default :

                break;
        }

        if ($key && array_key_exists($key, $array))
        {
            return $array[$key];
        }

        return $array;
    }


    /**
     * 自動でCRUDのフォームを生成した際にセットされるVALIDATION処理。
     * 現状特に利用はしていない。
     *
     * @param $factory
     * @return \Fuel\Core\Validation
     */
    public static function validate($factory)
    {
        $validate = \Validation::forge($factory);
        $validate->add_callable('\Utility\ExtensionValidationRule');
        $model_name = \Inflector::singularize(self::$_table_name);
        $_properties = self::getProperties();

        switch ($factory)
        {
            case 'delete':
            case 'status':
                $validate->add('id', __($model_name . '.label.id', [], 'id'))->add_rule('required');
                break;

            case 'create':
            case 'update':
                foreach (array_keys($_properties) as $key)
                {
                    if (isset(self::$_properties[\Str::lower($key)]))
                    {
                        if (isset(self::$_properties[\Str::lower($key)]['validation']))
                        {
                            $validation_rules = self::$_properties[\Str::lower($key)]['validation'];
                            $label_for_validation = __($model_name . '.label.' . \Str::lower($key), [], \Str::lower($key));

                            //required
                            if (in_array('required', $validation_rules))
                            {
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('required');
                            }

                            //email
                            if (in_array('valid_email', $validation_rules))
                            {
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('valid_email');
                            }

                            //URL
                            if (in_array('valid_url', $validation_rules))
                            {
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('valid_url');
                            }

                            //IP
                            if (in_array('valid_ip', $validation_rules))
                            {
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('valid_ip');
                            }

                            //match_pattern
                            if (array_key_exists('match_pattern', $validation_rules))
                            {
                                $match_pattern = reset($validation_rules['match_pattern']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('match_pattern', [$match_pattern]);
                            }

                            //numeric_between
                            if (array_key_exists('numeric_between', $validation_rules))
                            {
                                $between_min = reset($validation_rules['numeric_between']);
                                $between_max = end($validation_rules['numeric_between']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('numeric_between', [$between_min, $between_max]);
                            }

                            //multi_email
                            if (array_key_exists('valid_emails', $validation_rules))
                            {
                                $valid_emails = $validation_rules['valid_emails'];
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('valid_emails', $valid_emails);
                            }

                            //exact_length
                            if (array_key_exists('exact_length', $validation_rules))
                            {
                                $exact_length = reset($validation_rules['exact_length']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('exact_length', [$exact_length]);
                            }

                            //min_length
                            if (array_key_exists('min_length', $validation_rules))
                            {
                                $min_length = reset($validation_rules['min_length']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('min_length', [$min_length]);
                            }

                            //max_length
                            if (array_key_exists('max_length', $validation_rules))
                            {
                                $max_length = reset($validation_rules['max_length']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('max_length', [$max_length]);
                            }

                            //numeric_min
                            if (array_key_exists('numeric_min', $validation_rules))
                            {
                                $numeric_min = reset($validation_rules['numeric_min']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('numeric_min', [$numeric_min]);
                            }

                            //numeric_max
                            if (array_key_exists('numeric_max', $validation_rules))
                            {
                                $numeric_max = reset($validation_rules['numeric_max']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('numeric_max', [$numeric_max]);
                            }

                            //is_unique
                            if (array_key_exists('is_unique', $validation_rules))
                            {
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('is_unique', $validation_rules['is_unique']);
                            }

                            //is_different
                            if (array_key_exists('is_different', $validation_rules))
                            {
                                $is_different = reset($validation_rules['is_different']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('is_different', [$is_different]);
                            }

                            //is_identical
                            if (array_key_exists('is_identical', $validation_rules))
                            {
                                $is_identical = reset($validation_rules['is_identical']);
                                $validate->add(\Str::lower($key), $label_for_validation)->add_rule('is_identical', [$is_identical]);
                            }

                            //valid_transaction
                            if (array_key_exists('valid_transaction', $validation_rules))
                            {
                                $valid_transaction = $validation_rules['valid_transaction'];
                                if(is_array($valid_transaction) && count($valid_transaction) === 2)
                                {
                                    list($user_id, $vendor_id) = $validation_rules['valid_transaction'];
                                    $validate->add(\Str::lower($key), $label_for_validation)->add_rule('valid_transaction', [$user_id, $vendor_id]);
                                }
                            }
                        }
                    }
                }

                break;
        }

        return $validate;
    }


    /**
     * ユニークチェックをおこなう。
     * バリデーションクラス Validationを拡張子、同様のものを実装しているため、
     * 現状は特に利用していない。
     *
     * @param null $id
     * @param null $field_key
     * @param null $field_val
     * @return bool|int|mixed
     */
    public static function isUnique($id = null, $field_key = null, $field_val = null)
    {
        if (empty($field_key) || empty($field_val))
        {
            return true;
        }

        $query = static::query()->where($field_key, '=', $field_val);
        if ($id)
        {
            $query = $query->where('id', '!=', $id);
        }

        return $query->count() === 0;
    }


    /**
     * \DB::updateによりUPDATEをおこなう。
     * 特に利用していない。
     *
     * @param null $id
     * @param null $key
     * @param null $val
     * @return bool|null
     */
    public static function updateByQuery($id = null, $key = null, $val = null)
    {
        if (empty($id) || empty($key))
        {
            return false;
        }

        $param =
            [
                $key => $val,
            ];

        $class = static::getClass();
        $update = \DB::update(\Inflector::tableize($class));
        $rows_affected = $update->set($param)->where('id', $id)->execute();
        if (0 === intval($rows_affected))
        {
            return false;
        }

        return true;
    }


    /**
     * DBクラスを使って取得する。
     * 意図的にORMを使わない場合に利用。
     * 現状特に利用していない
     *
     * @param null $id
     * @return null
     */
    public static function findByQuery($id = null)
    {
        $class = static::getClass();
        $query = \DB::select()
            ->from(\Inflector::tableize($class))
            ->where('id', '=', $id)
            ->where('deleted_at', '=', null);

        if (empty($id) || 0 === $query->execute()->count())
        {
            return null;
        }

        list($result) = $query->as_object($class)->execute()->as_array();

        return $result;
    }


    /**
     * キャッシュを利用したい場合に利用。
     * 現状特に利用していない
     *
     * @param null $id
     * @param null $cache_identifier
     * @return null
     */
    public static function findC($id = null, $cache_identifier = null)
    {
        $class = static::getClass();
        if (empty($id) || empty($cache_identifier))
        {
            return null;
        }

        try
        {
            $query = \Cache::get($cache_identifier);
//				$query =\Cache::call($key, [__CLASS__, 'find']);
        }
        catch (\CacheNotFoundException $e)
        {
            $query = \Cache::call(
                $cache_identifier,
                [$class, 'find'],//Ex: \Model_Admin::find
                [
                    $id,
                ],
                60 * 10//10分間
            );//call and set to the cache
        }
        catch (\Exception $e)
        {
            $query = $class::find('all', ['where' => ['id' => $id]]);
        }
        finally
        {

        }

        return $query;
    }


    /**
     * 削除済みも含めてデータを取得したい場合に利用。
     * find　→　deleted_at <> NULL のレコードはHITしない
     * deleted_at <> NULL のレコードも検索したい場合、
     * 下記メソッドのように static::deletedを使うことで情報を取得できる。
     * 現状特に利用していない
     *
     * @param $id
     * @return null|\Orm\Model|\Orm\Model[]
     */
    public static function findEx($id = null)
    {
        if ($query = static::find($id))
        {
            return $query;
        }
        else
        {
            if ($query = static::deleted($id))
            {
                return $query;
            }
            else
            {
                return null;
            }
        }
    }


    /**
     * 特定のMODELへ追加、更新、削除をした場合、Model_Eventlogにデータを保存する
     * 現状特に利用していない
     *
     * @param null $object_id
     * @param null $object_at
     * @param null $object_by
     * @param null $model_action
     * @return bool
     */
    public static function afterSaveLog($object_id = null, $object_at = null, $object_by = null, $model_action = null)
    {
        switch (\Str::lower(str_replace("Model_", "", static::getClass())))
        {
            case "genre":
            case "subgenre":
            case "league":
            case "team":
            case "eventlog":
            case "session":
            case "upload":
            case "game":
            case "bettinggame":
                return true;

                break;

            case "bet":
            case "transaction":
                try
                {
                    $array = [
                        'model_name'   => static::getClass(),
                        'model_action' => $model_action,
                        'object_id'    => $object_id,
                        'object_at'    => $object_at,
                        'object_by'    => $object_by,
                        'ip_address'   => \Input::ip(),
                        'user_agent'   => \Input::user_agent(),
                        'segment1'     => \Uri::segment(1),
                        'segment2'     => \Uri::segment(2),
                        'segment3'     => \Uri::segment(3),
                    ];

                    //static::crudCreate()を使用すると無限ループするのでforge()を使用していることに注意。
                    $log = \Model_Eventlog::forge($array);
                    $log->save();
                    unset($array);
                }
                catch (\Exception $e)
                {
                    \Log::error($e);
                }
                finally
                {

                }

                break;

            default:
                return true;

                break;
        }

        return true;
    }


    /**
     * データの追加
     * 現状特に利用していない
     *
     * @param array $data
     * @return null|static
     */
    public static function crudCreate(array $data = [])
    {
        try
        {
            if (empty($data))
            {
                throw new \Exception(UpdateException);
            }

            $query = static::forge();
            foreach ($data as $k => $v)
            {
                if (is_array($k))
                {
                    foreach ($k as $kk => $vv)
                    {
                        $query->{$k}->{$kk} = $vv;
                    }
                }
                else
                {
                    $query->{$k} = $v;
                }
            }

            unset($data);

            if ($query->save())
            {
//					static::afterSaveLog($query->id, $query->created_at, $query->created_id, 'create');

                return $query;
            }

        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
        finally
        {

        }

        return null;
    }


    /**
     * データの更新
     * 現状特に利用していない
     *
     * @param null $id
     * @param array $data
     * @return null|\Orm\Model|\Orm\Model[]
     */
    public static function crudUpdate($id = null, array $data = [])
    {
        try
        {
            $query = static::find($id);
            if (empty($id) || empty($query) || empty($data) || !$query instanceof static)
            {
                throw new \Exception(UpdateException);
            }

            foreach ($data as $k => $v)
            {
                if (is_array($k))
                {
                    foreach ($k as $kk => $vv)
                    {
                        $query->{$k}->{$kk} = $vv;
                    }
                }
                else
                {
                    $query->{$k} = $v;
                }
            }

            unset($data);

            if (!$query->save())
            {
                throw new \Exception(UpdateException);
            }

//				static::afterSaveLog($query->id, $query->updated_at, $query->updated_id, 'update');

            return $query;
        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
        finally
        {

        }

        return null;
    }


    /**
     * データの読み込み
     * 現状特に利用していない
     *
     * @param null $id
     * @param array $data
     * @return null|\Orm\Model|\Orm\Model[]
     */
    public static function crudRead($id = null, array $data = [])
    {
        try
        {
            $query = static::find($id);
            if (empty($id) || empty($query) || empty($data))
            {
                throw new \Exception(UpdateException);
            }

            foreach ($data as $k => $v)
            {
                if (is_array($k))
                {
                    foreach ($k as $kk => $vv)
                    {
                        $query->{$k}->{$kk} = $vv;
                    }
                }
                else
                {
                    $query->{$k} = $v;
                }
            }

            unset($data);

            if ($query->save())
            {
                //static::afterSaveLog($query->id, $query->updated_at, $query->updated_id, 'update');

                return $query;
            }

        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
        finally
        {

        }

        return null;
    }


    /**
     * データの削除をする。
     * 現状特に利用していない
     *
     * @param null $id
     * @param null $deleted_id
     * @return null|\Orm\Model|\Orm\Model[]
     */
    public static function crudDelete($id = null, $deleted_id = null)
    {
        try
        {
            $query = static::crudUpdate($id, ['deleted_id' => $deleted_id]);
            if (empty($query))
            {
                throw new \Exception(DeleteException);
            }

            $query->delete();
            $deleted = static::deleted($id);
            //static::afterSaveLog($deleted->id, $deleted->deleted_at, $deleted->deleted_id, 'delete');

            return $deleted;
        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
        finally
        {

        }

        return null;
    }


    /**
     * ORMのFINDを拡張した形。
     *　$idは モデルのインスタンスを直接渡しても動作するし、login_auth　のようなオブジェクト、配列でも動作する。
     * 基本的に Model_Xxx::find()の形は使わず、このfindReal を使うことを推奨。
     *
     * @param null $id 数値またはモデルインスタンスまたは['id']要素を含む配列、オブジェクト
     * @return Model_ABCore|null|\Orm\Model|\Orm\Model[]
     */
    public static function findReal($id = null)
    {
        $model = null;
        if($id instanceof static)
        {
            $model = $id;
        }
        else
        {
            if(is_numeric($id))
            {
                $model = static::find($id);
            }
            else
            {
                if(is_array($id) && !empty($id['id']) && array_key_exists('id', $id))
                {
                    $model = static::find($id['id']);
                }
                else
                {
                    if(is_object($id) && !empty($id->id))
                    {
                        $model = static::find($id->id);
                    }
                }
            }
        }

        if (empty($id) || empty($model) || $id === 'all' || !$model instanceof static)
        {
            return null;
        }

        return $model;
    }


    /**
     * status = active の件数を取得する
     * 現状特に利用していない？
     *
     * @param array $where
     * @return mixed
     */
    public static function activeCount($where = [])
    {
        if (0 === count($where))
        {
            return static::query()->where('status', '=', 'open')->count();
        }

        return static::query()->where('status', '=', 'active')->count();
    }


    /**
     * 言語一覧やベンダー一覧生成などに利用している。
     *
     * @param null $column　表示に使用されるカラム name,username など
     * @param array $query_condition　WHERE条件
     * @param bool $include_default　DEFAULTの『選択して下さい』　を含めるかどうか？
     * @return array
     */
    public static function getQueryAsArray($column = null, $query_condition = [], $include_default = false)
    {
        $array = [];
        $where = \Arr::merge([['id', '!=', null]], $query_condition);
        $query = static::query()->where(\Fuel\Core\Arr::merge($where, $query_condition))->order_by(['id' => 'asc'])->get();
        if ($include_default)
        {
            $array = [null => __('asianbet.msg.please_select', [], '')];
        }

        foreach ($query as $key => $val)
        {
            $array[$query[$key]->id] = $query[$key]->{$column};
        }

        unset($query);

        return $array;
    }


    /**
     * 特定のcolumnの値のみ返す
     *
     * @param null $id
     * @param null $key_field
     * @return mixed|null
     */
    public static function getValue($id = null, $key_field = null)
    {
        $query = static::find($id);
        if (empty($id) || empty($query) || empty($key_field))
        {
            return null;
        }

        return $query->get($key_field);
    }


    /**
     * たとえば、USERNAMEなど一意なもので検索する場合に有効だが、
     * 現状特に利用していない？
     *
     * @param null $field_key
     * @param null $field_value
     * @return null|\Orm\Model
     */
    public static function getOneByField($field_key = null, $field_value = null)
    {
        if (empty($field_key) || empty($field_value))
        {
            return null;
        }

        if (empty(static::$_properties[$field_key]))
        {
            return null;
        }

        $query = static::query([$field_key, $field_value]);
        $item = $query->get_one();

        return $item;
    }


    /**
     * 一覧テーブルからのアクションリンク
     * 現状特に利用していない
     *
     * @param null $namespace
     * @param null $action
     * @param null $id
     * @param null $button_text
     * @param array $additional_parameters
     * @return null|string
     */
    public static function renderActionButton($namespace = null, $action = null, $id = null, $button_text = null, 	$additional_parameters = [])
    {
        $title = __('asianbet.' . $action, [], $action);
        $icon_class = 'fa ';
        $button_class = 'btn ';
        $additional_attributes = null;
        $element_before = '';
        $element_after = '';

        //TODO AB ACL 関連でリンクを制御
        switch ($action)
        {
            case 'create':
                $url = \Uri::Create($namespace . DS . $action);
                $icon_class .= 'fa-plus-circle';
                $button_class .= 'btn-primary item-create';
                $button_text = $title;

                break;

            case 'update':
                if (empty($id))
                {
                    return null;
                }

                $url = \Uri::Create($namespace . DS . $action . DS . $id);
                $icon_class .= 'fa-pencil';
                $button_class .= 'btn-sm btn-primary item-update';

                break;

            case 'delete':
                if (empty($id))
                {
                    return null;
                }

                $url = \Uri::Create($namespace . DS . $action . DS . $id);
                $icon_class .= 'fa-times';
                $button_class .= 'btn-sm btn-default item-delete';
                $additional_attributes['data-toggle'] = 'modal';
                $additional_attributes['data-target'] = '#delete-modal';
                $additional_attributes['data-id'] = $id;
                $additional_attributes['data-url'] = $url;
                $additional_attributes['href'] = "#";

                break;

            case 'reload':
                $url = \Uri::Create($namespace);
                $icon_class .= 'fa-refresh';
                $button_class .= 'btn-primary ajax-reload item-reload';

                break;

            case 'hint':
                $url = '#';
                $icon_class .= 'fa-question';
                $button_class .= 'btn-primary item-hint';
                $button_text = html_tag('span',
                    [
                        "class"               => "item-hint-container popover-dismiss",
                        "data-toggle"         => "popover",
                        "data-placement"      => "bottom",
                        "data-content"        => $button_text,
                        "data-original-title" => __('asianbet.hint', [], 'hint')
                    ]
                );

                break;

            case 'user':
                $url = '#';
                $icon_class .= 'fa-share-square-o';
                $button_class .= 'btn-primary btn-xs item-user';
                $additional_attributes['data-id'] = $id;
                $button_text = null;

                break;

            case 'collapse':
                $url = '#';
                $icon_class .= 'fa-angle-down';
                $button_class = 'item-collapse';

                break;

            case 'fullscreen':
                $url = '#';
                $icon_class .= 'fa-expand';
                $button_class = 'item-fullscreen';

                break;

            case 'hide':
                $url = '#';
                $icon_class .= 'fa-times';
                $button_class = 'item-remove';

                break;

            case 'balance':
                $url = '#';
                $icon_class .= 'fa-times';
                $button_class = 'item-balance';

                break;

            default:
                return null;

        }

        $anchor_attributes = [
            'rel'                 => 'tooltip',
            'data-toggle'         => 'tooltip',
            'data-original-title' => $title,
            'data-placement'      => 'bottom',
            'class'               => $button_class,
            'href'                => $url,
        ];

        if ($additional_attributes && is_array($additional_attributes))
        {
            $anchor_attributes = array_merge($anchor_attributes, $additional_attributes);
        }

        $icon = html_tag('i', ['class' => $icon_class]);

        return $element_before . html_tag('a', $anchor_attributes, $icon . $button_text) . $element_after;
    }


    /**
     * $_propertiesの設定値から自動的にテーブルのTDを描画する
     * 現状特に利用していない
     *
     * @param null $text
     * @param array $attributes
     * @return string
     */
    public static function renderTableTd($text = null, $attributes = [])
    {
        return html_tag('td', $attributes, $text);
    }


    /**
     * テーブル自動生成用。
     * $_propertiesの設定値から自動的にソートリンク付きのTHを出力してくれる。
     * 現状特に利用していない
     *
     * @param null $label
     * @param array $attributes
     * @return null|string
     */
    public static function renderTableTh($label = null, $attributes = [])
    {
        if (empty($label))
        {
            return $label;
        }

        return html_tag('th', $attributes, $label);
    }


    /**
     * 現状特に利用していない
     *
     * @param null $item
     * @param null $key
     * @param null $value
     * @param array $attributes
     * @return string
     */
    public static function renderTableTdByDataColumn($item = null, $key = null, $value = null, $attributes = [])
    {
        $output = null;
        if (empty($item) || empty($key) || empty($value) || empty($item->{$key}))
        {
            return static::renderTableTd($output, ['class' => 'text-center']);
        }

        if (!is_array($value))
        {
            return static::renderTableTd($item->{$key}, ['class' => 'text-center']);
        }

        isset($value['data_type']) or $value['data_type'] = 'varchar';
        $value['data_type'] = \Str::lower($value['data_type']);

        isset($value['align']) or $value['align'] = 'center';
        $value['align'] = \Str::lower($value['align']);

        switch ($value['data_type'])
        {
            //bitはうまく設定出来ない（保存できない）ため、tinyintでBOOL値は保存すること
            case 'tinyint' :
                if (intval($item->{$key}))
                {
                    $output = static::successIcon();
                }
                else
                {
                    $output = static::errorIcon();
                }

                break;

            case 'enum' :
                if (isset($value['form']) && isset($value['form']['options']))
                {
                    if (array_key_exists($item->{$key}, $value['form']['options']))
                    {
                        $icon = static::getIcon($item->{$key});
                        $output = $icon . $value['form']['options'][$item->{$key}];
                    }
                }

                break;

            //data_type = bigint,has_relationをカラムに設定した場合、自動的にFORMにリレーションを設定する
            case 'bigint' :
                if (isset($value['has_relation'])
                    && isset($value['has_relation']['type'])
                    && isset($value['has_relation']['condition'])
                    && isset($value['has_relation']['name'])
                    && isset($value['has_relation']['display_value'])
                )
                {
                    $value['has_relation']['condition'] = [
                        'where' => [
                            [
                                'id', $item->{$key}
                            ]
                        ],
                        'limit' => 1
                    ];

                    $relation_models = static::getRelations($value['has_relation']);
                    $display_value = $value['has_relation']['display_value'];
                    if ($relation_models && $display_value)
                    {
                        $relation_model = $relation_models->get_one();
                        if(isset($relation_model->{$display_value}))
                        {
                            $output = $relation_model->{$display_value};
                        }
                    }
                }
                else
                {
                    if(isset($item->{$key}))
                    {
                        $output = $item->{$key};
                    }
                }

                break;

            case 'decimal':
                $output = n($item->{$key} * 10000 / 10000);
                break;

            //TODO AB 他にもあれば適切に処理をする
            default:
            case 'varchar' :
            case 'smallint' :
            case 'bit' :
            case 'int' :
                if(isset($item->{$key}))
                {
                    $output = $item->{$key};
                }

                break;
        }

        $text_align = 'text-' . $value['align'];
        $html_class = $text_align;
        if(!empty($attributes) && is_array($attributes) && array_key_exists('class', $attributes))
        {
            $html_class .= ' ' . $attributes['class'];
        }
        $attributes['class'] = $html_class;

        return static::renderTableTd($output, $attributes);
    }


    /**
     * AJAXで一覧テーブルのリロードするためのJSをレンダリング
     * 現状特に利用していない
     *
     * @param null $namespace
     * @return string
     */
    public static function renderTableReloadJavascript($namespace = null)
    {
        $javascript = null;
        if (empty($namespace))
        {
            return $javascript;
        }

        $param = ['reload' => true];
        $url = \Uri::create($namespace) . '?' . http_build_query($param);
        $javascript = '<script type="text/javascript">
$(document).ready(function()
{
	try
	{
		$(".ajax-reload").on(
			"click", function (e)
			{
				e.preventDefault();
//				var block = $(this).parents(".block");
				var block = $(this).parent().parent().parent();
				var $search_form = $("#' . static::$search_form . '");
				if($search_form.length === 0)
				{
					return false;
				}

				var $search_param = $search_form.serializeArray();
				var $options =
				{
					url: "' . $url . '",
					type: "POST",
					dataType: "html",
					timeout: 60000,
					statusCode:
					{
    					403: function()
    					{
      						//return sessionExpired();
    					}
    				},
					data: $search_param
				};

				App.ContainerRefresh(block, true),
				App.ApiRequest($options).always(
					function (response)
					{
						setTimeout(function()
						{
							App.ContainerRefresh(block, false);
						},
						500);
					}
				).done(
					function (response)
					{
						$(".table-data-body").html(response);

						var $event = new $.Event("CompleteTableLoad");
						$(App).trigger($event, {function: App.CompleteTableLoad()});
					}
				).fail(
					function (response)
					{
						if(response.status === 403)
						{
							return App.sessionExpired();
						}

						throw new Error();
					}
				);
			}
		);
	}
	catch (e)
	{
		//alert(e.message);
	}
	finally
	{
		//
	}
});' . PHP_EOL;
        $javascript .= '</script>' . PHP_EOL;

        return $javascript;
    }


    /**
     * ASCかどうか？
     * @var string
     */
    public static $sortable_is_asc = 'ASC';


    /**
     * DESCかどうか？
     * @var string
     */
    public static $sortable_is_desc = 'DESC';


    /**
     * ASC, DESC
     * @var array
     */
    public static $sortable_order = ['ASC', 'DESC'];


    /**
     * 検索フォーム
     * @var string
     */
    public static $search_form = 'search_form';


    /**
     * 検索フォームのトグルボタン
     * @var string
     */
    public static $search_toggle_button = 'search_form_toggle';


    /**
     * 検索フォーム　送信ボタン
     * @var string
     */
    public static $search_submit_button = 'search_submit_button';


    /**
     * 検索フォーム　クリアボタン
     * @var string
     */
    public static $search_clear_button = 'search_clear_button';


    /**
     * 検索件数　HIDDEN
     * @var string
     */
    public static $search_limit_count_hidden = 'search_limit_count';


    /**
     * ページ数　HIDDEN　FIELD
     * @var string
     */
    public static $search_page_number_hidden = 'search_page_number';


    /**
     * 検索件数　選択
     * @var string
     */
    public static $search_limit_count_select = 'search_limit_count_select';


    /**
     * 検索件数　初期値
     * @var int
     */
    public static $search_default_limit_count = 25;


    /**
     * 検索フォームのCOOKIE用
     * @var string
     */
    public static $search_cookie_name = 'search_cookie';


    /**
     * 検索フォームの入力要素のプレフィクス
     * @var string
     */
    public static $search_input_prefix = "search_";


    /*
     * 表示件数
     */
    public static $search_limit_options = [
        '10'  => '10',
        '25'  => '25',
        '50'  => '50',
        '75'  => '75',
        '100' => '100',
    ];


    /**
     * 検索フォーム用JSを自動生成
     * 現状特に利用していない
     *
     * @return string
     */
    public static function renderSearchJavascript()
    {
        $output = '<script type="text/javascript">
function buildCookiePath()
{
	var $path = location.pathname;
	var $paths = [];
	$paths = $path.split("/");
	if($paths[$paths.length-1] != "")
	{
		$paths[$paths.length-1] = "";
		$path = $paths.join("/");
	}
	return $path;
}

var search_form_cookie_path = "' . '/' . \Uri::segment(1) . '/' . \Uri::segment(2) . '/' . \Uri::segment(3) . '";
$(document).ready(
	function()
	{
		$(".' . static::$sortable_link_class . '").on(
			"click", function (e)
			{
				e.preventDefault();
				var $sort = $(this).data("sort");
				var $order = $(this).data("order");
				if (typeof $sort !== "undefined" && typeof $order !== "undefined")
				{
					 $("#' . static::$sortable_order_hidden . '").val($order).change();
					 $("#' . static::$sortable_sort_hidden . '").val($sort).change();
					 $("#' . static::$search_form . '").submit();
				}
			}
		);

		$(".pagination > li > a").on(
			"click", function (e)
			{
				e.preventDefault();
				var $page = $(this).data("page");
				var $current = $("#' . static::$search_page_number_hidden . '").val();
				var $max_count = $(".pagination > li").size()
				- $(".pagination > li > a[data-page=prev]").size()
				- $(".pagination > li > a[data-page=next]").size()
				- $(".pagination > li > a[data-page=first]").size()
				- $(".pagination > li > a[data-page=last]").size()
				- $(".pagination > li > a[data-page=active]").size();

				if (typeof $page != "undefined")
				{
					if(isNaN($page) || typeof $page === "string" || $page instanceof String)
					{
						switch ($page)
						{
							case "prev"   : $page = parseInt($current) - 1;  break;
							case "next"   : $page = parseInt($current) + 1;  break;
							case "first"  : $page = 1;                       break;
							case "last"   : $page = parseInt($max_count);    break;
							case "active" :                  				 break;
							default       : 				                 break;
						}
					}

					 $("#' . static::$search_page_number_hidden . '").val($page).change();
					 $("#' . static::$search_form . '").submit();
				}
			}
		);

		$("#' . static::$search_limit_count_select . '").on(
			"change", function(e)
			{
				$("#' . static::$search_limit_count_hidden . '").val($(this).val()).change();
				$("#' . static::$search_form . '").submit();
			}
		);

		$("#' . static::$search_toggle_button . '").on(
			"click", function(e)
			{
				e.preventDefault();
				var $parent = $("#' . static::$search_form . '").parent();
				if($parent.is(":visible"))
				{
//					$.removeCookie("' . static::$search_cookie_name . '", { path: buildCookiePath(search_form_cookie_path) });
					Cookies.remove("' . static::$search_cookie_name . '");
				}
				else
				{
//					$.cookie("' . static::$search_cookie_name . '", 1, { path: buildCookiePath(search_form_cookie_path) });
					Cookies.set("' . static::$search_cookie_name . '", 1, {path: buildCookiePath(search_form_cookie_path)});
				}

				$parent.toggle();
				if($(this).find("i").is(".fa"))
				{
					$(this).find("i").toggleClass("fa-minus-square-o").toggleClass("fa-plus-square-o");
				}
			}
		);

		$("#' . static::$search_submit_button . '").on(
			"click", function(e)
			{
				e.preventDefault();
				$(this).button("loading");
				$(this).attr("disabled","disabled");
				$(this).parents("form").submit();
			}
		);

		$("#' . static::$search_clear_button . '").on(
			"click", function(e)
			{
				e.preventDefault();
				///$(this).parents("form").find("textarea, :text, select").val("").end().find(":checked").prop("checked", false);
				$(this).parents("form").find("textarea, :text").val("").end().find(":checked").prop("checked", false).end().find("select").val(null).end();
			}
		);

		if(Cookies.get("' . static::$search_cookie_name . '") == "1")
//		if($.cookie("' . static::$search_cookie_name . '") == "1")
		{
			$("#' . static::$search_toggle_button . '").trigger("click");
		}
	}
);' . PHP_EOL;

        $output .= '</script>' . PHP_EOL;

        return $output;
    }


    /**
     * 表示件数選択用SELECT
     * 現状特に利用していない
     *
     * @return string
     */
    public static function renderSearchPagerSelect()
    {
        $limit_count = \Input::post('limit_count', static::$search_default_limit_count);
        $input_attributes = ['class' => 'form-control pull-left', 'id' => static::$search_limit_count_select];

        return \Form::select('limit_count', $limit_count, static::$search_limit_options, $input_attributes);
    }


    /**
     * 検索フォーム生成
     * 現状特に利用していない
     *
     * @param array $form_attributes
     * @param array $search_params
     * @return string
     */
    public static function renderSearchForm($form_attributes = [], $search_params = [])
    {
        //For Form params
        $form_attributes['action'] = (isset($form_attributes['action'])) ? $form_attributes['action'] : \Uri::main();
        $form_attributes['method'] = (isset($form_attributes['method'])) ? $form_attributes['method'] : 'post';
        $form_attributes['class'] = (isset($form_attributes['class'])) ? $form_attributes['class'] : 'form-inline';
        $form_attributes['role'] = (isset($form_attributes['role'])) ? $form_attributes['role'] : 'form';
        $form_attributes['id'] = static::$search_form;
        $form_attributes['name'] = static::$search_form;

        //For Hidden params
        $hidden_attributes = [];

        //CREARFIX
        $crearfix = html_tag('div', ['class' => 'clearfix', 'style' => 'margin-bottom:15px'], html_tag('br'));

        //Toggle Button
        $i_toggle = html_tag('i', ['class' => 'fa fa-minus-square-o'], '');
        $search_toggle_button = \Html::anchor('#', $i_toggle . '&nbsp;' . __('asianbet.search', [], 'search'),
            ['id' => static::$search_toggle_button, 'class' => 'btn btn-primary pull-left', 'style' => 'margin-left:15px']);

        //Form
        $form = \Form::open($form_attributes, $hidden_attributes);
        //$form .= \Form::fieldset_open(['class' => '', 'id' => '', 'legend' => __('asianbet.search')]);

        //Hidden Limit Count
        $search_limit_count_hidden = html_tag('div', ['class' => 'hidden'],
            \Form::hidden('limit_count', \Input::post('limit_count', static::$search_default_limit_count), ['id' => static::$search_limit_count_hidden])
            . \Form::hidden('order', \Input::post('order', 'id'), ['id' => static::$sortable_order_hidden])
            . \Form::hidden('sort', \Input::post('sort', static::$sortable_is_asc), ['id' => static::$sortable_sort_hidden]));

        $form .= $search_limit_count_hidden;

        $search_page_number_hidden = html_tag('div', ['class' => 'hidden'],
            \Form::hidden('page', \Input::post('page', 1), ['id' => static::$search_page_number_hidden]));
        $form .= $search_page_number_hidden . $crearfix;

        $cnt = 0;
        foreach ($search_params as $key => $val)
        {
            //For input params
            $val['value'] = (isset($val['value'])) ? $val['value'] : null;
            $val['type'] = (isset($val['type'])) ? $val['type'] : "text";
            $val['options'] = (isset($val['options'])) ? $val['options'] : ['' => ''];
            $val['label'] = (isset($val['label'])) ? $val['label'] : null;
            $val['multiple'] = (isset($val['multiple'])) ? $val['multiple'] : null;

            $div = null;
            $input_attributes = ['class' => 'form-control col-md-12', 'id' => static::$search_input_prefix . $key, 'style' => 'width:100%'];
            $label = html_tag('div', ['class' => 'col-md-3'],
                \Form::label($val['label'],
                    static::$search_input_prefix . $key,
                    ['for' => static::$search_input_prefix . $key, 'class' => '']));

            switch ($val['type'])
            {
                case 'text' :
                    $input = html_tag('div', ['class' => 'col-md-9'],
                        \Form::input($key, $val['value'], $input_attributes));
                    $div = html_tag('div', ['class' => 'form-group col-md-6'], $label . $input);
                    break;

                case 'range' :
                case 'time' :
                case 'datetime' :
                case 'date' :
                    $input_attributes_for_date = [
                        'class' => 'form-control col-md-12 ' . $val['type'] . 'picker',
                        'id'    => static::$search_input_prefix . $key, 'style' => 'width:100%'
                    ];
                    $input = html_tag('div', ['class' => 'col-md-9'],
                        \Form::input($key, $val['value'], $input_attributes_for_date));

                    $div = html_tag('div', ['class' => 'form-group col-md-6'], $label . $input);
                    unset($input_attributes_for_date);
                    break;

                case 'checkbox' :
                case 'radio' :
                    $input = null;
                    $i = 0;
                    foreach ($val['options'] as $k => $v)
                    {
                        if (method_exists('\Form', $val['type']) && is_callable('\Form' . '::' . $val['type']))
                        {
                            $val['checked'] = \Str::lower(trim($k)) === \Str::lower(trim($val['value']));
                            $input_attributes = [
                                'class' => 'form-' . $val['type'],
                                'id'    => static::$search_input_prefix . $key . '_' . $i
                            ];

                            $input .= html_tag('div', ['class' => $val['type'] . '-inline'],
                                call_user_func('\Form' . '::' . $val['type'], $key, $k, $val['checked'], $input_attributes)
                                . \Form::label($v, static::$search_input_prefix . $key, ['for' => static::$search_input_prefix . $key . '_' . $i])
                            );

                            $i++;
                        }
                    }

                    $div = html_tag('div', ['class' => 'form-group  col-md-6 '], $label . html_tag('div', ['class' => 'col-md-9'], $input));
                    break;

                case 'select' :
                    $select_input_attributes = [
                        'class'          => 'form-control col-md-12 ' . $val['type'],
                        'id'             => static::$search_input_prefix . $key,
                        $val['multiple'] => $val['multiple'],
                        'style'          => 'width:100%'
                    ];

                    $input = html_tag('div', ['class' => 'col-md-9'],
                        \Form::select($key, $val['value'], $val['options'], $select_input_attributes));
                    $div = html_tag('div', ['class' => 'form-group col-md-6'], $label . $input);

                    break;

                case 'hidden' :
                    $input_attributes_for_hidden = ['class' => 'form-control', 'id' => static::$search_input_prefix . $key];
                    $input = html_tag('div', ['class' => 'col-md-9'],
                        \Form::hidden($key, $val['value'], $input_attributes_for_hidden));
                    $div = html_tag('div', ['class' => 'hidden'], $input);
                    unset($input_attributes_for_hidden);

                    break;

                default :
                    $div = null;
                    $input = null;
                    break;

            }

            if ($div)
            {
                if ($val['type'] !== 'hidden')
                {
                    $cnt++;
                }

                if (($cnt + 2) % 2 === 0)
                {
                    $div = $div . $crearfix;
                }

                $form .= $div;
            }
            unset($div, $input, $label);
        }

        if (count($search_params) % 2 === 1)
        {
            $form .= html_tag('div', ['class' => 'form-group col-md-6'], '&nbsp;') . $crearfix;
        }

        //Submit Button,Clear Button
        $i_search = html_tag('i', ['class' => 'fa fa-search'], '');
        $i_times = html_tag('i', ['class' => 'fa fa-times'], '');

        $search_submit_button = \Html::anchor('#', $i_search, ['id' => static::$search_submit_button, 'class' => 'btn btn-primary', 'style' => '']);
        $search_clear_button = \Html::anchor('#', $i_times, ['id' => static::$search_clear_button, 'class' => 'btn btn-default', 'style' => '']);
        if (count($search_params) > 0)
        {
            $form .= html_tag('div', ['class' => 'form-group col-md-12 btn-group'], $search_submit_button . $search_clear_button);
        }

        //$form .= \Form::fieldset_close();
        $form .= \Form::close();

        $panel_heading = html_tag('div', ['class' => 'search-form-heading'], $crearfix . html_tag('h3', ['class' => 'search-form-title clearfix'], $search_toggle_button));
        $panel_body = html_tag('div', ['class' => 'search-form-body'], $form) . $crearfix;

        return html_tag('div', ['class' => 'search-form-panel', 'padding' => '5px'], $panel_heading . $panel_body);
    }


    /**
     * ソート用リンクのクラス
     *
     * @var string
     */
    public static $sortable_link_class = 'sortable_link';


    /**
     * ソート　HIDDEN用フィールド
     * 検索フォームにセットされる。
     *
     * @var string
     */
    public static $sortable_order_hidden = 'sortable_order';


    /**
     * @var string
     */
    public static $sortable_sort_hidden = 'sortable_sort';


    /**
     * ソートリンクを生成
     * 現状特に利用していない
     *
     * @param string $order
     * @param null $link
     * @param string $link_text
     * @param array $attributes
     * @param null $secure
     * @return string
     */
    public static function sortableLink($order = 'id', $link = null, $link_text = '', $attributes = [], $secure = null)
    {
        $link or $link = \Uri::main();
        if (\Str::lower(\Input::post('order', 'id')) === \Str::lower($order))
        {
            if (in_array(\Input::post('sort', static::$sortable_is_asc), static::$sortable_order))
            {
                if (\Str::lower(\Input::post('sort', static::$sortable_is_asc)) === \Str::lower(static::$sortable_is_asc))
                {
                    $sort = static::$sortable_is_desc;
                    $class = 'sorting_asc';
                }
                else
                {
                    $sort = static::$sortable_is_asc;
                    $class = 'sorting_desc';
                }
            }
            else
            {
                $sort = static::$sortable_is_asc;
                $class = 'sorting';
            }
        }
        else
        {
            $sort = static::$sortable_is_asc;
            $class = 'sorting';
        }

        $query = array_merge(
            \Input::post(),
            [
                'order' => $order,
                'sort'  => $sort
            ]
        );

        if (isset($query['page']))
        {
            unset($query['page']);
        }

        $attributes['data-sort'] = $sort;
        $attributes['data-order'] = $order;

//			$querystring = http_build_query($query);
//			$link .= '?' . $querystring;

        if (isset($attributes['class']))
        {
            $attributes['class'] .= ' ' . $class;
        }
        else
        {
            $attributes['class'] = $class;
        }

        $attributes['class'] .= ' ' . static::$sortable_link_class;

        return \Html::anchor($link = '#', $link_text, $attributes, $secure);
    }
}
