<?php
/**
 * Widget: Lead / quotation form builder (stores submissions in "درخواست‌ها" and e-mails them).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lead form widget.
 */
class Larijani_Widget_Lead_Form extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-lead-form';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS فرم استعلام / مشاوره', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}
	/** @return array */
	public function get_keywords() {
		return array( 'form', 'contact', 'فرم', 'تماس', 'استعلام' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_layout', __( 'چیدمان', 'larijani-stone' ) );
		$this->ctl(
			'layout',
			'select',
			__( 'چیدمان', 'larijani-stone' ),
			'split',
			array(
				'options' => array(
					'split'      => __( 'ستون اطلاعات + فرم', 'larijani-stone' ),
					'split-card' => __( 'کارت بزرگ (اطلاعات + فرم داخل کارت)', 'larijani-stone' ),
					'form'       => __( 'فقط فرم', 'larijani-stone' ),
				),
			)
		);
		$this->ctl( 'bare', 'switch', __( 'بدون کانتینر بخش (برای ستون‌های المنتور)', 'larijani-stone' ), '' );
		$this->ctl( 'card_style', 'select', __( 'رنگ کارت فرم', 'larijani-stone' ), 'surface', array( 'options' => array( 'surface' => __( 'کارت سفید، فیلد کرم', 'larijani-stone' ), 'canvas' => __( 'کارت کرم، فیلد سفید', 'larijani-stone' ) ) ) );
		$this->bg_control( 'surface' );
		$this->end();

		$this->section( 'sec_info', __( 'ستون اطلاعات', 'larijani-stone' ), 'content', array( 'layout!' => 'form' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), 'مشاوره مالی و صنعتی' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani-stone' ), 'calculator' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani-stone' ), 'محاسبه سرمایه اولیه و دریافت طرح توجیهی خط تولید', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'مشخصات کارگاه و نیاز خود را ثبت کنید تا کارشناسان لاریجانی استون ظرف حداکثر ۲ ساعت کاری جهت برآورد هزینه دقیق ماشین‌آلات، مقدار قالب مورد نیاز و مشاوره تخصصی با شما تماس بگیرند.' );
		$this->ctl( 'info_style', 'select', __( 'سبک موارد', 'larijani-stone' ), 'cards', array( 'options' => array( 'cards' => __( 'کارت با آیکون', 'larijani-stone' ), 'checks' => __( 'لیست تیک‌دار', 'larijani-stone' ) ) ) );
		$this->rep(
			'info_items',
			__( 'موارد', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'telephone-inbound' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'text', 'text', __( 'متن', 'larijani-stone' ), '' ),
				array( 'link', 'url', __( 'لینک متن', 'larijani-stone' ), '' ),
				array( 'ltr', 'switch', __( 'متن چپ‌به‌راست (شماره تلفن)', 'larijani-stone' ), '' ),
			),
			array(
				array( 'icon' => 'telephone-inbound', 'title' => 'ارتباط تلفنی مستقیم با مهندس لاریجانی', 'text' => '۰۹۱۲ ۲۳۰ ۲۶۸۵ / ۰۹۳۵ ۴۴۳ ۱۳۲۱', 'ltr' => 'yes' ),
				array( 'icon' => 'buildings', 'title' => 'آدرس کارخانه ماشین‌سازی', 'text' => 'قزوین، آبیک، مجتمع صنعتی پیروز، پلاک ۱۵' ),
				array( 'icon' => 'download', 'title' => 'کاتالوگ و لیست قیمت جامع', 'text' => 'دانلود فایل PDF لیست قیمت بهمن ۱۴۰۴ (رایگان)', 'link' => '#' ),
			)
		);
		$this->end();

		$this->section( 'sec_form_head', __( 'سربرگ فرم', 'larijani-stone' ) );
		$this->ctl( 'form_kicker', 'text', __( 'متن کوچک بالای فرم', 'larijani-stone' ), '' );
		$this->ctl( 'form_kicker_icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'receipt' );
		$this->ctl( 'form_title', 'text', __( 'عنوان فرم', 'larijani-stone' ), '' );
		$this->ctl( 'form_desc', 'textarea', __( 'توضیح فرم', 'larijani-stone' ), '' );
		$this->end();

		$this->section( 'sec_fields', __( 'فیلدها', 'larijani-stone' ) );
		$this->ctl( 'form_name', 'text', __( 'نام فرم (برای شناسایی در درخواست‌ها)', 'larijani-stone' ), 'مشاوره راه‌اندازی خط تولید' );
		$this->rep(
			'fields',
			__( 'فیلدها', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'type', 'select', __( 'نوع', 'larijani-stone' ), 'text', array( 'options' => array( 'text' => __( 'متن', 'larijani-stone' ), 'tel' => __( 'تلفن', 'larijani-stone' ), 'email' => __( 'ایمیل', 'larijani-stone' ), 'number' => __( 'عدد', 'larijani-stone' ), 'select' => __( 'کشویی', 'larijani-stone' ), 'checkboxes' => __( 'چندانتخابی', 'larijani-stone' ), 'textarea' => __( 'متن چندخطی', 'larijani-stone' ), 'file' => __( 'آپلود فایل', 'larijani-stone' ), 'consent' => __( 'تیک تأیید', 'larijani-stone' ) ) ) ),
				array( 'name', 'text', __( 'نام لاتین فیلد (اختیاری)', 'larijani-stone' ), '' ),
				array( 'placeholder', 'text', __( 'متن راهنما', 'larijani-stone' ), '' ),
				array( 'options', 'textarea', __( 'گزینه‌ها (هر خط یکی؛ با * پیش‌فرض تیک‌خورده)', 'larijani-stone' ), '', array( 'rows' => 4 ) ),
				array( 'required', 'switch', __( 'اجباری', 'larijani-stone' ), '' ),
				array( 'width', 'select', __( 'عرض', 'larijani-stone' ), 'half', array( 'options' => array( 'half' => __( 'نصف', 'larijani-stone' ), 'full' => __( 'کامل', 'larijani-stone' ) ) ) ),
				array( 'icon', 'icon', __( 'آیکون داخل فیلد', 'larijani-stone' ), '' ),
				array( 'ltr', 'switch', __( 'ورود چپ‌به‌راست', 'larijani-stone' ), '' ),
				array( 'hint', 'text', __( 'متن زیر فیلد آپلود', 'larijani-stone' ), '' ),
			),
			array(
				array( 'label' => 'نام و نام خانوادگی', 'type' => 'text', 'name' => 'name', 'placeholder' => 'مثال: مهندس رضوانی', 'required' => 'yes' ),
				array( 'label' => 'شماره تماس همراه', 'type' => 'tel', 'name' => 'phone', 'placeholder' => '۰۹۱۲...', 'required' => 'yes', 'ltr' => 'yes' ),
				array( 'label' => 'شهر محل احداث کارگاه', 'type' => 'text', 'name' => 'city', 'placeholder' => 'مثال: شیراز، تبریز، رشت...', 'required' => 'yes' ),
				array( 'label' => 'متراژ تقریبی کارگاه / سالن', 'type' => 'select', 'name' => 'scale', 'options' => "کارگاه کوچک (۵۰ تا ۱۰۰ متر مربع)\nکارگاه استاندارد (۱۰۰ تا ۳۰۰ متر مربع)\nسوله صنعتی بزرگ (بیش از ۳۰۰ متر مربع)\nهنوز فضایی اجاره/تامین نشده است" ),
				array( 'label' => 'خدمات مورد نظر شما (چند انتخابی):', 'type' => 'checkboxes', 'name' => 'services', 'width' => 'full', 'options' => "*خرید قالب‌های نشکن ABS (نما و کف)\n*ساخت دستگاه میز ویبره و میکسر صنعتی\n*آموزش حضوری در محل کارگاه مشتری\nتأمین رنگدانه‌های معدنی و رزین تخصصی" ),
				array( 'label' => 'توضیحات تکمیلی یا سوال فنی:', 'type' => 'textarea', 'name' => 'notes', 'width' => 'full', 'placeholder' => 'در مورد ظرفیت تولید، برق کارگاه، میزان بودجه یا نوع محصولات مد نظر خود توضیح دهید...' ),
			),
			'{{{ label }}}'
		);
		$this->end();

		$this->section( 'sec_submit', __( 'ارسال و پیام موفقیت', 'larijani-stone' ) );
		$this->ctl( 'submit_text', 'text', __( 'متن دکمه', 'larijani-stone' ), 'ثبت درخواست مشاوره فنی و استعلام خط تولید' );
		$this->ctl( 'submit_icon', 'icon', __( 'آیکون دکمه', 'larijani-stone' ), 'send' );
		$this->ctl( 'submit_full', 'switch', __( 'دکمه تمام‌عرض', 'larijani-stone' ), 'yes' );
		$this->ctl( 'note', 'text', __( 'یادداشت کنار دکمه', 'larijani-stone' ), '' );
		$this->ctl( 'success_title', 'text', __( 'عنوان پیام موفقیت', 'larijani-stone' ), '' );
		$this->ctl( 'success_text', 'textarea', __( 'متن پیام موفقیت', 'larijani-stone' ), 'درخواست شما با موفقیت ثبت گردید. کارشناسان ما ظرف ۲ ساعت آینده جهت هماهنگی و محاسبه اولیه با شما تماس می‌گیرند.', array( 'description' => __( 'از {tracking} برای نمایش کد پیگیری استفاده کنید.', 'larijani-stone' ) ) );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render the form element.
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	protected function form_html( $s ) {
		$input_bg = 'canvas' === $s['card_style'] ? 'bg-surface-card shadow-sm' : 'bg-surface-canvas';
		$base     = 'w-full ' . $input_bg . ' text-surface-dark rounded-xl font-body-md text-body-md placeholder:text-outline-variant focus:bg-white focus:shadow-sm focus:ring-2 focus:ring-primary-container/30 transition-all';
		$uid      = $this->get_id();
		ob_start();
		?>
		<form class="flex flex-col gap-space-md" data-ls-form enctype="multipart/form-data" novalidate>
			<?php echo larijani_form_hidden_fields( $s['form_name'] ); // phpcs:ignore ?>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
			<?php foreach ( $s['fields'] as $i => $f ) : ?>
				<?php
				$key   = $f['name'] ? sanitize_key( $f['name'] ) : 'field_' . $i;
				$id    = 'lsf-' . $uid . '-' . $key;
				$req   = 'yes' === $f['required'];
				$span  = 'full' === $f['width'] || in_array( $f['type'], array( 'textarea', 'checkboxes', 'file', 'consent' ), true ) ? 'sm:col-span-2' : '';
				$icon  = ! empty( $f['icon']['value'] );
				$ltr   = 'yes' === $f['ltr'] ? ' dir="ltr"' : '';
				$align = 'yes' === $f['ltr'] ? ' text-right' : '';
				?>
				<div class="flex flex-col gap-1.5 <?php echo esc_attr( $span ); ?>">
					<input type="hidden" name="labels[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( wp_strip_all_tags( $f['label'] ) ); ?>">
					<input type="hidden" name="types[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $f['type'] ); ?>">
					<?php if ( 'consent' !== $f['type'] ) : ?>
					<label class="font-label-nav text-label-nav text-surface-dark flex items-center gap-1" for="<?php echo esc_attr( $id ); ?>"><span><?php echo esc_html( $f['label'] ); ?></span><?php if ( $req ) : ?><span class="text-error">*</span><?php endif; ?></label>
					<?php endif; ?>

					<?php if ( 'select' === $f['type'] ) : ?>
					<select id="<?php echo esc_attr( $id ); ?>" name="fields[<?php echo esc_attr( $key ); ?>]" class="<?php echo esc_attr( $base ); ?> px-space-md py-2.5 cursor-pointer"<?php echo $req ? ' required' : ''; ?>>
						<?php foreach ( larijani_lines( $f['options'] ) as $opt ) : ?>
						<option value="<?php echo esc_attr( ltrim( $opt, '*' ) ); ?>"<?php selected( 0 === strpos( $opt, '*' ) ); ?>><?php echo esc_html( ltrim( $opt, '*' ) ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php elseif ( 'checkboxes' === $f['type'] ) : ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-body-sm font-body-sm text-on-surface-variant">
						<?php foreach ( larijani_lines( $f['options'] ) as $opt ) : ?>
						<label class="flex items-center gap-2 p-2 rounded-lg <?php echo esc_attr( $input_bg ); ?> cursor-pointer hover:bg-surface-container transition-colors">
							<input class="rounded" type="checkbox" name="fields[<?php echo esc_attr( $key ); ?>][]" value="<?php echo esc_attr( ltrim( $opt, '*' ) ); ?>"<?php checked( 0 === strpos( $opt, '*' ) ); ?>>
							<span><?php echo esc_html( ltrim( $opt, '*' ) ); ?></span>
						</label>
						<?php endforeach; ?>
					</div>
					<?php elseif ( 'textarea' === $f['type'] ) : ?>
					<textarea id="<?php echo esc_attr( $id ); ?>" name="fields[<?php echo esc_attr( $key ); ?>]" class="<?php echo esc_attr( $base ); ?> p-space-md resize-none" rows="3" placeholder="<?php echo esc_attr( $f['placeholder'] ); ?>"<?php echo $req ? ' required' : ''; ?>></textarea>
					<?php elseif ( 'file' === $f['type'] ) : ?>
					<label class="<?php echo esc_attr( $input_bg ); ?> hover:bg-surface-container border-2 border-dashed border-outline-variant/60 rounded-2xl p-space-md flex flex-col items-center justify-center gap-space-xs cursor-pointer transition-colors group text-center" for="<?php echo esc_attr( $id ); ?>">
						<span class="w-12 h-12 rounded-full bg-surface-card flex items-center justify-center text-primary group-hover:scale-105 transition-transform shadow-sm"><i class="bi bi-cloud-arrow-up text-[22px]" aria-hidden="true"></i></span>
						<span class="font-headline-sm text-body-md text-on-surface"><?php echo esc_html( $f['placeholder'] ? $f['placeholder'] : __( 'انتخاب فایل از سیستم یا کشیدن به این بخش', 'larijani-stone' ) ); ?></span>
						<?php if ( $f['hint'] ) : ?><span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $f['hint'] ); ?></span><?php endif; ?>
						<input class="sr-only" id="<?php echo esc_attr( $id ); ?>" type="file" name="ls_files[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.dwg,.zip" data-ls-file>
						<span class="font-label-badge text-label-badge text-accent-emerald mt-1 font-bold" data-ls-file-names></span>
					</label>
					<?php elseif ( 'consent' === $f['type'] ) : ?>
					<label class="flex items-center gap-space-sm pt-space-xs cursor-pointer">
						<input class="w-4 h-4 rounded cursor-pointer" type="checkbox" name="fields[<?php echo esc_attr( $key ); ?>]" value="<?php esc_attr_e( 'بله', 'larijani-stone' ); ?>"<?php echo $req ? ' required' : ' checked'; ?>>
						<span class="text-body-sm font-body-sm text-on-surface-variant"><?php echo esc_html( $f['label'] ); ?></span>
					</label>
					<?php else : ?>
					<div class="relative">
						<?php if ( $icon ) : ?><?php echo larijani_icon( $f['icon'], 'absolute right-3.5 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none' ); // phpcs:ignore ?><?php endif; ?>
						<input id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( $f['type'] ); ?>" name="fields[<?php echo esc_attr( $key ); ?>]" class="<?php echo esc_attr( $base . $align ); ?> <?php echo $icon ? 'pr-10 pl-3.5' : 'px-space-md'; ?> py-2.5" placeholder="<?php echo esc_attr( $f['placeholder'] ); ?>"<?php echo $ltr; // phpcs:ignore ?><?php echo $req ? ' required' : ''; ?><?php echo 'tel' === $f['type'] ? ' inputmode="tel" autocomplete="tel"' : ''; ?>>
					</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			</div>

			<div class="hidden p-space-md bg-secondary-container text-on-secondary-container rounded-2xl items-start gap-space-sm" data-ls-success>
				<i class="bi bi-check-circle-fill text-accent-emerald text-[22px] shrink-0" aria-hidden="true"></i>
				<div class="flex flex-col text-body-md font-body-md">
					<?php if ( $s['success_title'] ) : ?><span class="font-bold"><?php echo esc_html( $s['success_title'] ); ?></span><?php endif; ?>
					<span data-ls-success-text="<?php echo esc_attr( $s['success_text'] ); ?>"><?php echo esc_html( str_replace( '{tracking}', '—', $s['success_text'] ) ); ?></span>
				</div>
			</div>
			<div class="hidden ls-form-error" data-ls-error></div>

			<div class="pt-space-xs flex flex-col sm:flex-row items-center gap-space-md <?php echo $s['note'] && ! $this->on( $s, 'submit_full' ) ? 'justify-between' : ''; ?>">
				<?php if ( $s['note'] && ! $this->on( $s, 'submit_full' ) ) : ?>
				<span class="text-body-sm font-body-sm text-outline flex items-center gap-1 order-2 sm:order-1"><i class="bi bi-lock-fill text-[14px]" aria-hidden="true"></i><?php echo esc_html( $s['note'] ); ?></span>
				<?php endif; ?>
				<button class="<?php echo $this->on( $s, 'submit_full' ) ? 'w-full' : 'w-full sm:w-auto px-space-xl order-1 sm:order-2'; ?> inline-flex items-center justify-center gap-2 bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav py-3 rounded-full shadow-md hover:shadow-lg transition-all" type="submit">
					<?php echo larijani_icon( $s['submit_icon'], 'text-[17px]' ); // phpcs:ignore ?><span><?php echo esc_html( $s['submit_text'] ); ?></span>
				</button>
				<?php if ( $s['note'] && $this->on( $s, 'submit_full' ) ) : ?>
				<span class="text-body-sm font-body-sm text-outline flex items-center gap-1"><i class="bi bi-lock-fill text-[14px]" aria-hidden="true"></i><?php echo esc_html( $s['note'] ); ?></span>
				<?php endif; ?>
			</div>
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Info column.
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	protected function info_html( $s ) {
		ob_start();
		?>
		<div class="flex flex-col gap-space-md">
			<?php if ( $s['badge'] ) : ?>
			<div class="inline-flex items-center gap-1.5 self-start bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full text-label-badge font-label-badge w-fit"><?php echo larijani_icon( $s['badge_icon'], 'text-[14px]' ); // phpcs:ignore ?><span><?php echo esc_html( $s['badge'] ); ?></span></div>
			<?php endif; ?>
			<h2 class="font-headline-lg text-headline-lg text-surface-dark tracking-tight"><?php echo $this->t( $s, 'title' ); // phpcs:ignore ?></h2>
			<?php if ( $s['desc'] ) : ?><p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p><?php endif; ?>
			<?php if ( $s['info_items'] ) : ?>
				<?php if ( 'checks' === $s['info_style'] ) : ?>
				<div class="flex flex-col gap-2 text-body-sm font-body-sm text-surface-dark pt-space-xs">
					<?php foreach ( $s['info_items'] as $it ) : ?>
					<div class="flex items-center gap-2"><?php echo larijani_icon( ! empty( $it['icon']['value'] ) ? $it['icon'] : 'bi bi-check-circle-fill', 'text-accent-emerald text-[16px]' ); // phpcs:ignore ?><span><?php echo esc_html( trim( $it['title'] . ' ' . $it['text'] ) ); ?></span></div>
					<?php endforeach; ?>
				</div>
				<?php else : ?>
				<div class="flex flex-col gap-space-sm pt-space-xs">
					<?php foreach ( $s['info_items'] as $it ) : ?>
					<div class="flex items-center gap-space-sm p-space-sm bg-surface-canvas rounded-xl">
						<div class="w-10 h-10 rounded-lg bg-surface-card flex items-center justify-center text-primary shadow-sm shrink-0 text-[20px]"><?php echo larijani_icon( $it['icon'] ); // phpcs:ignore ?></div>
						<div class="flex flex-col">
							<span class="font-title-card text-title-card text-surface-dark"><?php echo esc_html( $it['title'] ); ?></span>
							<?php if ( ! empty( $it['link']['url'] ) ) : ?>
							<a class="font-body-sm text-body-sm text-primary hover:underline" <?php echo larijani_link_attrs( $it['link'] ); // phpcs:ignore ?>><?php echo esc_html( $it['text'] ); ?></a>
							<?php else : ?>
							<span class="<?php echo 'yes' === $it['ltr'] ? 'font-label-nav text-label-nav' : 'font-body-sm text-body-sm'; ?> text-on-surface-variant"<?php echo 'yes' === $it['ltr'] ? ' dir="ltr"' : ''; ?>><?php echo esc_html( $it['text'] ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Form card head.
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	protected function head_html( $s ) {
		if ( ! $s['form_kicker'] && ! $s['form_title'] && ! $s['form_desc'] ) {
			return '';
		}
		$out = '<div class="flex flex-col gap-space-xs pb-space-lg">';
		if ( $s['form_kicker'] ) {
			$out .= '<div class="inline-flex items-center gap-2 text-primary font-bold text-body-sm">' . larijani_icon( $s['form_kicker_icon'], 'text-[18px]' ) . '<span>' . esc_html( $s['form_kicker'] ) . '</span></div>';
		}
		if ( $s['form_title'] ) {
			$out .= '<h2 class="font-headline-md text-headline-md text-on-surface">' . esc_html( $s['form_title'] ) . '</h2>';
		}
		if ( $s['form_desc'] ) {
			$out .= '<p class="font-body-md text-body-md text-on-surface-variant">' . esc_html( $s['form_desc'] ) . '</p>';
		}
		return $out . '</div>';
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$card_bg = 'canvas' === $s['card_style'] ? 'bg-surface-canvas' : 'bg-surface-card';
		$form    = '<div class="' . esc_attr( $card_bg ) . ' p-space-lg lg:p-space-xl rounded-3xl shadow-md">' . $this->head_html( $s ) . $this->form_html( $s ) . '</div>';
		if ( $this->on( $s, 'bare' ) && 'form' === $s['layout'] ) {
			echo $form; // phpcs:ignore
			return;
		}
		?>
		<section class="w-full py-space-2xl <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<?php if ( 'form' === $s['layout'] ) : ?>
					<?php echo $form; // phpcs:ignore ?>
				<?php elseif ( 'split-card' === $s['layout'] ) : ?>
				<div class="relative bg-surface-card rounded-3xl p-space-lg lg:p-space-2xl shadow-xl overflow-hidden">
					<div class="absolute top-0 left-0 w-32 h-32 bg-secondary-container/40 rounded-br-full pointer-events-none"></div>
					<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center relative z-10">
						<div class="lg:col-span-6"><?php echo $this->info_html( $s ); // phpcs:ignore ?></div>
						<div class="lg:col-span-6 <?php echo esc_attr( $card_bg ); ?> p-space-lg rounded-2xl shadow-sm"><?php echo $this->head_html( $s ) . $this->form_html( $s ); // phpcs:ignore ?></div>
					</div>
				</div>
				<?php else : ?>
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
					<div class="lg:col-span-5"><?php echo $this->info_html( $s ); // phpcs:ignore ?></div>
					<div class="lg:col-span-7"><?php echo $form; // phpcs:ignore ?></div>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
