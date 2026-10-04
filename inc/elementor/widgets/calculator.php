<?php
/**
 * Widget: LS ماشین‌حساب مصرف (رزین).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Calculator.
 */
class LS_Widget_Calculator extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-calculator';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS ماشین‌حساب مصرف (رزین)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-calculator';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani' ), 'محاسبه‌گر مصرف رزین' );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'فرمول LS-500' );
		$this->ctl( 'desc', 'text', __( 'توضیح', 'larijani' ), 'وزن سیمان مصرفی در هر بچ اختلاط میکسر را وارد کنید:' );
		$this->ctl( 'input_label', 'text', __( 'برچسب ورودی', 'larijani' ), 'سیمان مصرفی در میکسر:' );
		$this->ctl( 'unit', 'text', __( 'واحد ورودی', 'larijani' ), 'کیلوگرم' );
		$this->ctl( 'min', 'number', __( 'حداقل', 'larijani' ), 50 );
		$this->ctl( 'max', 'number', __( 'حداکثر', 'larijani' ), 600 );
		$this->ctl( 'step', 'number', __( 'گام', 'larijani' ), 25 );
		$this->ctl( 'value', 'number', __( 'مقدار پیش‌فرض', 'larijani' ), 100 );
		$this->ctl( 'out1_label', 'text', __( 'خروجی ۱ – عنوان', 'larijani' ), 'رزین پیشنهادی:' );
		$this->ctl( 'out1_factor', 'number', __( 'خروجی ۱ – ضریب', 'larijani' ), 0.009, array( 'step' => 0.001 ) );
		$this->ctl( 'out1_unit', 'text', __( 'خروجی ۱ – واحد', 'larijani' ), 'کیلوگرم' );
		$this->ctl( 'out2_label', 'text', __( 'خروجی ۲ – عنوان', 'larijani' ), 'حداکثر آب مجاز:' );
		$this->ctl( 'out2_factor', 'number', __( 'خروجی ۲ – ضریب', 'larijani' ), 0.28, array( 'step' => 0.01 ) );
		$this->ctl( 'out2_unit', 'text', __( 'خروجی ۲ – واحد', 'larijani' ), 'لیتر' );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		ls_render_resin_calculator(
			array(
				'title' => $s['title'],
				'badge' => $s['badge'],
				'desc' => $s['desc'],
				'input_label' => $s['input_label'],
				'unit' => $s['unit'],
				'min' => $s['min'],
				'max' => $s['max'],
				'step' => $s['step'],
				'value' => $s['value'],
				'out1_label' => $s['out1_label'],
				'out1_factor' => $s['out1_factor'],
				'out1_unit' => $s['out1_unit'],
				'out2_label' => $s['out2_label'],
				'out2_factor' => $s['out2_factor'],
				'out2_unit' => $s['out2_unit'],
			)
		);
	}
}
