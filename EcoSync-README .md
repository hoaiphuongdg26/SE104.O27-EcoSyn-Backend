1. Run localhost
   => php artisan serve

2. Duplicate ".env.example" and rename file ".env"

3. Application Key (if not exist)
   => php artisan key:generate

4. Create database
   => php artisan migrate

#Laravel v10.48.2
#PHP v8.1.13
---------KIT----------
Route::get($uri, $callback) - nhận resquest với phương thức GET.
Route::post($uri, $callback) - nhận resquest với phương thức POST.
=> Route::get('welcome', function () {
return "Xin chào";
});
