<?php
Route::group(['middleware' => 'auth'], function()
{

AdvancedRoute::controller('addon', 'AddOnController');
AdvancedRoute::controller('calendar', 'CalendarController');
AdvancedRoute::controller('cities', 'CitiesController');
AdvancedRoute::controller('quotation', 'QuotationController');
AdvancedRoute::controller('airports', 'AirportsController');
AdvancedRoute::controller('countries', 'CountriesController');
AdvancedRoute::controller('currency', 'CurrencyController');
AdvancedRoute::controller('extraexpenses', 'ExtraexpensesController');
AdvancedRoute::controller('expenses', 'ExpensesController');
AdvancedRoute::controller('expenseitems', 'ExpenseItemsController');
AdvancedRoute::controller('extraservices', 'ExtraservicesController');
AdvancedRoute::controller('hotelcategories', 'HotelcategoriesController');
AdvancedRoute::controller('hotelfacilities', 'HotelfacilitiesController');
AdvancedRoute::controller('hoteltypes', 'HoteltypesController');
AdvancedRoute::controller('guidelanguages', 'GuidelanguagesController');
AdvancedRoute::controller('optionaltours', 'OptionaltoursController');
AdvancedRoute::controller('paymenttypes', 'PaymenttypesController');
AdvancedRoute::controller('roomamenities', 'RoomamenitiesController');
AdvancedRoute::controller('roomtypes', 'RoomtypesController');
AdvancedRoute::controller('shoppingtypes', 'ShoppingtypesController');
AdvancedRoute::controller('sites', 'SitesController');
AdvancedRoute::controller('staffs', 'StaffController');
AdvancedRoute::controller('suppliertypes', 'SuppliertypesController');
AdvancedRoute::controller('suppliers', 'SuppliersController');
AdvancedRoute::controller('tourcategories', 'TourcategoriesController');
AdvancedRoute::controller('transfer', 'VehicletypesController');
AdvancedRoute::controller('guide', 'GuideController');
AdvancedRoute::controller('guidenotes', 'GuidenotesController');
AdvancedRoute::controller('groupsales', 'GroupsalesController');
AdvancedRoute::controller('teams', 'TeamsController');
AdvancedRoute::controller('bookinggroups', 'BookgroupsController');
AdvancedRoute::controller('accounting', 'AccountingController');
AdvancedRoute::controller('teamtypes', 'TeamtypesController');
AdvancedRoute::controller('tourdates', 'TourdatesController');
AdvancedRoute::controller('tours', 'ToursController');
AdvancedRoute::controller('travellers', 'TravellersController');
AdvancedRoute::controller('parkingandadmissionfees', 'ParkingandadmissionfeesController');
AdvancedRoute::controller('packages', 'PackagesController');
AdvancedRoute::controller('hotels', 'HotelsController');
AdvancedRoute::controller('travelagents', 'TravelagentsController');
AdvancedRoute::controller('invoice', 'InvoiceController');
AdvancedRoute::controller('payments', 'PaymentsController');
AdvancedRoute::controller('hotelrates', 'HotelratesController');
AdvancedRoute::controller('tourdetail', 'TourdetailController');
AdvancedRoute::controller('testimonials', 'TestimonialsController');
AdvancedRoute::controller('tandc', 'TandcController');
AdvancedRoute::controller('calendartourdates', 'CalendartourdatesController');
AdvancedRoute::controller('travellersnote', 'TravellersnoteController');
AdvancedRoute::controller('hotelsnote', 'HotelsnoteController');
AdvancedRoute::controller('agents', 'AgentsController');
AdvancedRoute::controller('travelagentnote', 'TravelagentnoteController');
AdvancedRoute::controller('createbooking', 'CreatebookingController');
AdvancedRoute::controller('booktour', 'BooktourController');
AdvancedRoute::controller('bookroom', 'BookroomController');
AdvancedRoute::controller('bookhotel', 'BookhotelController');
AdvancedRoute::controller('bookflight', 'BookflightController');
AdvancedRoute::controller('airlines', 'AirlinesController');
AdvancedRoute::controller('cars', 'CarsController');
AdvancedRoute::controller('carbrands', 'CarbrandsController');
AdvancedRoute::controller('carextras', 'CarextrasController');
AdvancedRoute::controller('bookcar', 'BookcarController');
AdvancedRoute::controller('bookextra', 'BookextraController');
AdvancedRoute::controller('log', 'LogController');
AdvancedRoute::controller('inclusions', 'InclusionsController');
AdvancedRoute::controller('support', 'SupportController');
AdvancedRoute::controller('faqs', 'FaqsController');
AdvancedRoute::controller('commentscheck', 'CommentscheckController');
AdvancedRoute::controller('travellersfiles', 'TravellersfilesController');
AdvancedRoute::controller('visaapplication', 'VisaapplicationController');

AdvancedRoute::controller('tickets', 'TicketsController');
Route::post('/tickets/store', 'TicketsController@store')->name('tickets.store');
Route::get('/tickets/edit/get', 'TicketsController@getEdit')->name('tickets.getEdit');
Route::post('/tickets/update/{id}', 'TicketsController@update')->name('tickets.update');

Route::group(['prefix' => 'staffs', 'as' => 'staffs.'], function () {
    Route::get('/', 'StaffController@index')->name('index');
    Route::post('/store', 'StaffController@store')->name('store');
    Route::get('/edit', 'StaffController@edit')->name('edit');
    Route::post('/update/{id}', 'StaffController@update')->name('update');
});

Route::group(['prefix' => 'stafftypes', 'as' => 'stafftypes.'], function () {
    Route::get('/', 'StaffTypeController@index')->name('index');
    Route::post('/store', 'StaffTypeController@store')->name('store');
    Route::get('/edit', 'StaffTypeController@edit')->name('edit');
    Route::post('/update/{id}', 'StaffTypeController@update')->name('update');
});

});
?>
