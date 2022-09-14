=== Booking X - Appointment and Reservation Availability Calendar ===
Contributors: dunskii, dparekh19
Tags: appointments, appointment booking, availability calendar, booking calendar, booking system, reservations, reservation calendar
Requires at least: 5.0
Tested up to: 6.0.2
Requires PHP: 7.0
Stable tag: 1.0.14
License: GPLv2 or later
License URI: https://booking-x.com/gpl-licence/

Booking X is a simple to use booking system that allows you to take appointments and reservations for multiple business types on WordPress.

== Description ==
**Booking X** is a flexible *WordPress plugin* that allows you to create, accept, and **manage bookings** for your business.

It's your complete **booking plugin** for your **service** business, allowing for **unlimited** resources, staff and services.

Perfect for **Hairdressers** & **Beauty Salons**.
Enable your clients to make their next **appointment**, anywhere, anytime, straight from your web site. Allowing you to focus on growing your business.

Use it for your **Product Hire** and **Room Bookings**.
*Booking X* is flexible enough to allow your to customers to **book** or **hire** your resrouces, be it a power saw, moving van or meeting room, for a few hours or a few days.

Helping **Personal Trainers** and Consultants stay organised.
**Schedule** that next business meeting or training session. Display your services and take payment all from the same, simple to use platform.

It is a flexible WordPress **booking plugin** that allows you to create, accept, and **manage bookings** for your business. You can access and arrange **bookings** all in one place. The seamless workflow from posting your **bookings** to managing payments are just a few clicks away.

See Everything in Your **Schedule**.
The **booking calendar** with multiple views makes it easy to manage your resources and staff. Having an agenda, weekly, and monthly view allows you to focus on what’s important.

**Manage Bookings** in Your Personalized Dashboards.
The personalized dashboards will help you focus on each staff member and **booking**. Manage staff, products, or space on different dashboards. You won’t miss a business opportunity as you can see all **bookings** clearly.

**Expand the functionality of Booking X by installing [Add ons](https://booking-x.com/add-ons/)**

* Group a selection of services together and create packages for your customers to choose from with **[Booking Packages](https://booking-x.com/downloads/booking-packages/)**.
* Keep your staff energetic, by ensuring that they don't get a booking over their break with **[Breaks and Vactions for Staff](https://booking-x.com/downloads/breaks-vacation-for-staff/)**.
* Let customers know how great your services are with **[Ratings and Reviews](https://booking-x.com/downloads/ratings-and-reviews/)**.
* Do you offer services that span time zones? Then you want to let your customers know what your availability  is in their time zone using **[Time Zone](https://booking-x.com/downloads/time-zone/)**.
* Start accepting payments with **[Razorpay](https://booking-x.com/downloads/razorpay-payment-gateway/)**.
* Accept credit card payments and more using **[Stripe Payments](https://booking-x.com/downloads/stripe-payments/)**.
* Make it simple to use Booking X and Divi together using our **[Divi Booking X Module](https://booking-x.com/downloads/divi-booking-x-module/)**.

== Installation ==

1. Backup your site completely before proceeding.
2. Download Booking X to your desktop. Extract the Booking X folder to your desktop.
3. With your FTP program, upload the Booking X folder to the wp-content/plugins folder in your WordPress directory online.
4. Go to Plugins screen and find the newly uploaded Plugin in the list. Click Activate to activate it.

**Basic configuration**
To have a basic install of Booking X, that will enable you to take bookings on your web site you will need to complete the following settings.

*Add your General Settings*
1. To add your businesses basic details go to Booking X -> Settings -> Business Information -> General Details.
2. The email and phone details will be how your customer gets in contact.
3. The address is where the customer will go for fixed location bookings.

*Add you Days of Operation*
To set your business hours go to Booking X -> Settings -> Business Information -> Days of Operation
As most businesses have the same operating hours for each day you can group these by:
1. Clicking the Choose a day drop down box and selecting the days that have the same operating hours.
2. Then setting the open and close times.
If you want to have another group of hours click the Add another set of hours button.
Days that aren’t selected will be set as closed on the booking form.

**To take payment at time of booking**

*Setup PayPal Express*
To take prepayment or deposits for a booking using PalPal Express go to Booking X -> Settings -> Payment -> PayPal Express
If you haven’t got your API credentials, you can learn how to create them here.
If you are currently developing your website, you can use the sandbox option to text the system without processing a real payment.

If you require another payment merchant, e.g. [Stripe Payments](https://booking-x.com/downloads/stripe-payments/), you can download these from our [add on marketplace](https://booking-x.com/add-ons/)

*Tax Settings*
You can add you regions tax settings by going to Booking X -> Settings -> Payment -> Tax Settings

*Set Currency*
You can set your countries currency by going to Booking X -> Settings -> Payment -> Currency


**Once you have confirmed your basic settings you will need to add at least 1 resource and 1 services**

*Add A Resource*
To add a resource go to Booking X -> Resource (This label will differ depending on what you set it as in your Alias settings)
Adding the Resource Name, Description and Image works the same as creating a page or post in WordPress.
1. Resource Name is the form field “Add Title”
2. Description in the block editor (or classic editor).
3. Set the feature image for the resource image.

In the Details section you can do the follow:
1. If the resource does the service at a different fixed location, you can change it here. The default will be address set in Business Information -> General Details
2. When a resource is only available certain months of the year, set “Will this Seat only be available certain months of the year:” to Yes and then select the months it is available.
3. By default Resources will use the Days of Operation as their availability. You can customise this by changing the days and hours. If you set this to No the resource will be available 24/7.
4. When a customer makes a booking and you want to take a deposit or full payment set “Will the booking require pre payment” to Yes.
4.a. You can then select if you want to take full payment or a deposit.
4.b. If you select deposit you can set payment as a fixed amount or Percent
5. If the resource is going to be a site user, set Do you want to associate this with a user to Yes.
5.a. If you would like the system to automatically create the user set :”Create User automatically” to Yes
6. Extra Notification details can be added


*Add A Service*
To add a resource go to Booking X -> Service (This label will differ depending on what you set it as in your Alias settings)
Adding the Service Name, Description and Image works the same as creating a page or post in WordPress.
1. Service Name is the form field “Enter Title Here”
2. Description in the visual editor
3. Set the feature image for the service image.

In the Details section you can do the following:
1. Set Price
2. Set Time type. E.g. Days, Hours, Minute
3. Set if the time can be extended. E.g. The default time for the service is 1 hour. If set to Yes, will allow a customer to book multiple of the set time.
4. Set which resources will be able to offer the service
5. Set if this service is at a fixed location (what was set in General Details or New Resource) or mobile.
6. If mobile is you you can then select if the service is mobile only or can also be offered at your fixed location.
7. Set if the service will offer extras (services only offered when booking another service)

**If you require further information on how to use Booking X please read our [tutorials](https://booking-x.com/tutorials)**

== Frequently Asked Questions ==
= Do you have a premium version? =

No, our goal is for Booking X to have all the core features that a service business requires to manage bookings. To extend its functionality we will be selling [add-ons](https://booking-x.com/add-ons/).

= Where can I get support? =

If you need help with Booking X, we recommend visiting our [website](https://booking-x.com/).

= Is Booking X multisite compatible? =

Yes, we love multisite. Our [demo](https://demo.booking-x.com) of Booking X is running it.

= Is Booking X compatible with my theme? =

It should be, remember you can set the colours to fit your site by going Settings -> General tab -> Styling

== Screenshots ==

1. Unavailable dates and times are highlighted when making a booking.
2. Tell customers as much as you can about your resources using the power of blocks (or the classic editor).
3. Let customers see everything about the services you offer.
4. As well as setting days of operation you can also set when you are closed for public holidays and business vacations.
5. Use Booking X blocks to display the booking form, as well as create resource, service and extras listing pages.

== Upgrade Notice ==

= 1.0.14 =
Fixed currency display issue - bug request by Roy.

== Updating ==
Automatic updates should work smoothly, but we still recommend you back up your site.

== Changelog ==

= 1.0 - Let's get this party started! 2021-16-03 =
= 1.0.1 - Latest WP version 5.7 capability update. 2021-30-03 =
= 1.0.2 - Latest WP version 5.7 capability update. 2021-30-03 =
= 1.0.3 - Adding Sale Price functionality. 2021-20-04 =
= 1.0.4 - Improvements to edit booking UI and UX. 2021-04-06 =
= 1.0.5 - Improvements to administration monthly and weekly booking views. 2021-18-06 =
= 1.0.6 - Added functionality to handle add-on licensing. UI improvements - including booking ordering. 2021-20-07 =
= 1.0.7 - Improved block functionality. 2021-24-09 =
= 1.0.8 - Minor Bug Fixes. 2022-19-02 =
= 1.0.9 - Minor bug fixes. Added option for customer to register during booking process. Custom email for customer registration. 2022-24-03 =
= 1.0.10 - Fixed backend booking form bug (Support Request). Fixed booking form block service selection issue. Fixed WP Debug error displaying on dashboard booking list.  2022-07-12 =
= 1.0.11 - Latest WP version 6.0 capability update.  2022-05-27 =
= 1.0.12 - Minor Bug Fixes.  2022-07-18 =
= 1.0.13 - Latest WP version 6.0.2 capability update.  2022-09-13 =
= 1.0.14 - Fixed currency display issue - bug request by Roy.  2022-09-14 =