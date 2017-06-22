# BookingX

# A booking and appointment plugin for WordPress

# Created with love by Dunskii Web Services 2017

# To install do the following
1. Backup your site completely before proceeding.
2. Download Booking X to your desktop. Extract the Booking X folder to your desktop.
3. With your FTP program, upload the BookingX folder to the wp-content/plugins folder in your WordPress directory online.
4. Go to Plugins screen and find the newly uploaded Plugin in the list. Click Activate to activate it.

#### Basic confiuration
To have a basic install of Booking X, that will enable you to take bookings on your web site you will need to complete the following settings.

# Add your General Settings
1. To add your businesses basic details go to BookingX -> Business Information -> General Details.
2. The email and phone details will be how your customer gets in contact.
3. The address is where the customer will go for fixed location bookings. (Yes, there is an option for mobile bookings.)

# Add you Days of Operation
To set your business hours go to BookingX -> Business Information -> Days of Operation
As most businesses have the same operating hours for each day you can group these by:
1. Clicking the Choose a day drop down box and selecting the days that have the same operating hours.
2. Then setting the open and close times.
If you want to have another group of hours click the Add another set of hours button.
Days that aren’t selected will be set as closed on the booking form.

# To take payment at time of booking 

#Setup PayPal Express
To take prepayment or deposits for a booking using PalPal Express go to BookingX -> Payment -> PayPal Express
If you haven’t got your API credentials, you can learn how to create them here.
If you are currently developing your website, you can use the sandbox option to text the system without processing a real payment.

# Tax Settings
You can add you regions tax settings by going to BookingX -> Payment -> Tax Settings

#Set Currency
You can set your countries currency by going to BookingX -> Settings -> Payment -> Currency


## Once you have confired your basic settings you will need to add at least 1 resource and 1 services

#Add A Resrouce
To add a resource go to BookingX -> Resource (This label will differ depending on what you set it as in your Alias settings)
Adding the Resource Name, Description and Image works the same as creating a page or post in WordPress.
1. Resource Name is the form field “Enter Title Here”
2. Description in the visual editor
3. Set the feature image for the resource image.

In the Details section you can do the follow:
1. If the resource does the service at a different fixed location (not mobile), you can change it here. The default will be address set in Business Information -> General Details
2. When a resource is only available certain months of the year, set “Will this Seat only be available certain months of the year:” to Yes and then select the months it is available.
3. By default Resources will use the Days of Operation as their availability. You can customise this by changing the days and hours. If you set this to No the resource will be available 24/7.
4. When a customer makes a booking and you want to take a deposit or full payment set “Will the booking require pre payment” to Yes.
4.a. You can then select if you want to take full payment or a deposit.
4.b. If you select deposit you can set payment as a fixed amount or Percent
5. If the resource is going to be a site user, set Do you want to associate this with a user to Yes.
5.a. If you would like the system to automatically create the user set :”Create User automatically” to Yes
6. Extra Notification details can be added


#Add A Service
To add a resource go to BookingX -> Service (This label will differ depending on what you set it as in your Alias settings)
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

## If you require further information on how to use Booking X please go to https://booking-x.com/tutorials ##