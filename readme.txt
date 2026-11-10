=== Contact Floating ===
Contributors: contactfloating
Tags: contact, floating, social, whatsapp, telegram, widget
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

دکمه شناور راه‌های ارتباطی — ساده، سریع و بدون وابستگی به Elementor یا jQuery.

== Description ==

Contact Floating یک افزونه سبک وردپرس برای نمایش دکمه شناور راه‌های ارتباطی است.

= امکانات =

* فعال/غیرفعال کردن ویجت
* موقعیت پایین راست یا پایین چپ
* نمایش در دسکتاپ، موبایل یا هر دو
* نمایش یا مخفی کردن عنوان کنار آیکون
* انیمیشن اختیاری
* تنظیم فاصله، اندازه، رنگ و بوردر دکمه اصلی
* مدیریت آیتم‌ها: نام، لینک، رنگ، آیکون SVG یا لینک SVG
* Drag & Drop برای ترتیب
* حداکثر ۲۰ آیتم
* شورت‌کد `[contact_floating]`
* پشتیبانی RTL و فارسی
* بدون jQuery و بدون Elementor

= Shortcode =

`[contact_floating]`

اگر شورت‌کد استفاده شود، نمایش خودکار فوتر برای جلوگیری از تکرار غیرفعال می‌شود.

== Installation ==

1. فایل ZIP را از طریق پیشخوان وردپرس → افزونه‌ها → افزودن نصب کنید.
2. افزونه را فعال کنید.
3. به Settings → Contact Floating بروید و آیتم‌ها را تنظیم کنید.

== Frequently Asked Questions ==

= آیا به Elementor نیاز دارد؟ =

خیر.

= آیا jQuery لازم است؟ =

خیر. فقط Vanilla JS استفاده شده است.

= چگونه SVG سفارشی اضافه کنم؟ =

در هر آیتم، آیکون را روی Custom SVG بگذارید و کد SVG یا لینک فایل SVG را وارد کنید.

== Changelog ==

= 1.0.4 =
* Hardened custom SVG sanitization
* Allowed contact URL schemes (tel/sms/whatsapp/…) for safe output
* Open http(s) links in new tab only; tel/mailto stay same-tab
* Safer admin icon preview rendering
* GitHub packaging prep (LICENSE, README, gitignore)

= 1.0.3 =
* بردر مشکی کم‌شفاف برای آیکون‌ها و لیبل‌ها (بدون shadow)
* سایز و فاصله ریسپانسیو برای تبلت و موبایل
* تنظیم جداگانه فاصله پایین/کنار موبایل برای نوبار

= 1.0.2 =
* حذف سایه از آیکون‌ها، لیبل‌ها و دکمه اصلی
* رفع ظاهر/محو شدن اسکرول‌بار هنگام باز شدن منو

= 1.0.1 =
* رفع تداخل استایل تم با پس‌زمینه دکمه اصلی
* دایره کامل شدن دکمه X در حالت باز
* تراز عمودی متن دکمه و ارث‌بری فونت سایت
* نمایش همه آیتم‌ها با اسکرول در صورت زیاد بودن
* بهبود اعتبارسنجی لینک‌های tel/mailto

= 1.0.0 =
* نسخه اول
