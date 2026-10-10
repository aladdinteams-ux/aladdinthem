<?php
defined('ABSPATH') || exit;
class Frpme_Component_Widget extends \Elementor\Widget_Base {
    protected $frpme_key;
    public function __construct($data=array(),$args=null){$this->frpme_key=isset($args['frpme_component']) ? $args['frpme_component'] : (isset($data['widgetType']) ? substr($data['widgetType'],6) : '');parent::__construct($data,$args);}
    protected function frpme_component(){ $components=frpme_data('components');return isset($components[$this->frpme_key]) ? $components[$this->frpme_key] : array('source'=>'home','title'=>'FRP','fields'=>array()); }
    public function get_initial_config(): array { $config=parent::get_initial_config();$config['show_in_panel']=false;return $config;}
    public function get_name(){return 'frpme-'.$this->frpme_key;}
    public function get_title(){return $this->frpme_component()['title'];}
    public function get_icon(){return 'eicon-code';}
    public function get_categories(){return array('frpme-'.$this->frpme_component()['source']);}
    public function get_style_depends(){ $source=$this->frpme_component()['source'];return array(in_array($source,array('header','footer'),true) ? 'frpme-chrome' : 'frpme-reference-'.$source,'frpme-bridge'); }
    public function get_script_depends(){ $source=$this->frpme_component()['source'];return array('frpme-runtime','frpme-program-'.('footer'===$source ? 'header' : $source)); }
    protected function register_controls(){
        $component=$this->frpme_component();$stored=get_option('frpme_content',array());$global=isset($stored[$this->frpme_key]) ? $stored[$this->frpme_key] : array();
        foreach(array('text'=>'متن‌ها و برچسب‌ها','attribute'=>'متن جایگزین و توضیحات','image'=>'تصاویر و رسانه','url'=>'لینک‌ها','icon'=>'آیکون‌ها') as $kind=>$label){
            $fields=array_filter($component['fields'],function($f)use($kind){return $f['kind']===$kind;});if(!$fields){continue;}
            $this->start_controls_section('frpme_group_'.$kind,array('label'=>$label));
            foreach($fields as $field){$default=isset($global[$field['key']]) ? $global[$field['key']] : $field['default'];$control=array('label'=>$field['label']);
                if('image'===$kind){$control+=array('type'=>\Elementor\Controls_Manager::MEDIA,'default'=>array('url'=>frpme_resolve($default)),'dynamic'=>array('active'=>true));}
                elseif('url'===$kind){$control+=array('type'=>\Elementor\Controls_Manager::URL,'options'=>array('url'),'default'=>array('url'=>frpme_resolve($default)),'dynamic'=>array('active'=>true));}
                elseif('icon'===$kind){$options=array();foreach(frpme_data('icons') as $icon){$options['#'.$icon]=$icon;}$control+=array('type'=>\Elementor\Controls_Manager::SELECT,'options'=>$options,'default'=>$default);}
                else{$control+=array('type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>$default,'dynamic'=>array('active'=>true));}
                $this->add_control('frpme_'.$field['key'],$control);
            }
            $this->end_controls_section();
        }
        if('blog-blog-library'===$this->frpme_key){$this->start_controls_section('frpme_posts',array('label'=>'مطالب واقعی'));$this->add_control('frpme_post_count',array('label'=>'تعداد در برگه سفارشی؛ آرشیو از تنظیمات وردپرس استفاده می‌کند','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>1,'max'=>48,'default'=>get_option('posts_per_page')));$this->end_controls_section();}
        $selector='{{WRAPPER}} [data-frpme-component="'.$this->frpme_key.'"]';
        $this->start_controls_section('frpme_style',array('label'=>'تنظیمات ظاهر اختیاری','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('frpme_background',array('label'=>'پس‌زمینه','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array($selector=>'background-color: {{VALUE}};')));
        $this->add_control('frpme_text_color',array('label'=>'رنگ متن','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array($selector=>'color: {{VALUE}};')));
        $this->add_control('frpme_padding',array('label'=>'فاصله داخلی','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px','em','%'),'selectors'=>array($selector=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
        $this->add_control('frpme_radius',array('label'=>'گردی کادر بخش','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>80)),'selectors'=>array($selector=>'border-radius: {{SIZE}}{{UNIT}};')));
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'frpme_typography','label'=>'تایپوگرافی بخش','selector'=>$selector));
        $this->end_controls_section();
    }
    protected function render(){frpme_render_component($this->frpme_key,$this->get_settings_for_display());}
}
class Frpme_Complete_Widget extends \Elementor\Widget_Base {
    public function get_initial_config(): array { $config=parent::get_initial_config();$config['show_in_panel']=false;return $config;}
    public function get_name(){return 'frpme-complete-page';}public function get_title(){return 'FRP / صفحه کامل با تنظیمات افزونه';}public function get_icon(){return 'eicon-document-file';}public function get_categories(){return array('general');}public function get_style_depends(){return array('frpme-bridge');}public function get_script_depends(){return array('frpme-runtime');}
    protected function register_controls(){$this->start_controls_section('frpme_page',array('label'=>'صفحه اصلی'));$this->add_control('frpme_source',array('label'=>'نوع صفحه','type'=>\Elementor\Controls_Manager::SELECT,'options'=>array('home'=>'صفحه اصلی','about'=>'درباره ما','services'=>'خدمات','contact'=>'تماس','blog'=>'وبلاگ','article'=>'مقاله'),'default'=>'home'));$this->end_controls_section();}
    protected function render(){$source=$this->get_settings_for_display('frpme_source');if(!isset(frpme_data('pages')[$source])){return;}wp_enqueue_style('frpme-reference-'.$source);wp_enqueue_script('frpme-program-'.$source);frpme_render_page($source);}
}
class Frpme_Legacy_Widget extends Frpme_Complete_Widget {
    private $frpme_legacy_source;
    public function __construct($data=array(),$args=null){$this->frpme_legacy_source=isset($args['frpme_source']) ? $args['frpme_source'] : (isset($data['widgetType']) ? substr($data['widgetType'],5) : 'home');parent::__construct($data,$args);}
    public function get_initial_config(): array { $config=parent::get_initial_config();$config['show_in_panel']=false;return $config;}
    public function get_name(){return 'frpm-'.$this->frpme_legacy_source;}public function get_title(){return 'FRP / سازگاری نسخه قبل / '.$this->frpme_legacy_source;}public function get_style_depends(){return array('frpme-reference-'.$this->frpme_legacy_source,'frpme-bridge');}public function get_script_depends(){return array('frpme-program-'.$this->frpme_legacy_source);}
    protected function register_controls(){ $this->start_controls_section('frpme_legacy',array('label'=>'محتوای نسخه قبل'));$this->add_control('frpme_info',array('type'=>\Elementor\Controls_Manager::RAW_HTML,'raw'=>'ویرایش‌های نسخه قبل هنگام نمایش حفظ می‌شوند. برای ویرایش بخش به بخش، الگوی جدید را در یک پیش‌نویس جدا وارد کنید.'));foreach(array('texts','images','links') as $name){$this->add_control($name,array('type'=>\Elementor\Controls_Manager::HIDDEN));}$this->end_controls_section(); }
    protected function render(){
        $all=frpme_data('legacy-map');$map=isset($all[$this->frpme_legacy_source]) ? $all[$this->frpme_legacy_source] : array();$settings=$this->get_settings_for_display();$overrides=array();
        foreach(array('texts','images','links') as $type){foreach(isset($settings[$type]) && is_array($settings[$type]) ? $settings[$type] : array() as $row){if(!isset($row['key'],$map[$row['key']])){continue;}$value='texts'===$type ? (isset($row['value']) ? $row['value'] : null) : ('images'===$type ? (isset($row['media']['url']) ? $row['media']['url'] : null) : (isset($row['url']['url']) ? $row['url']['url'] : null));if(null===$value){continue;}$target=$map[$row['key']];$overrides[$target['component']]['frpme_'.$target['field']]=$value;}}
        frpme_render_page($this->frpme_legacy_source,$overrides);
    }
}
