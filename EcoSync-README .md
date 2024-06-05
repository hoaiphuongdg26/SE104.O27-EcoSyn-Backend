#Laravel v10.48.2
#PHP v8.1.13
#MySQL 8.0.36
#Composer 2.7.2

http://ecosync:8000

1. Run localhost
   => php artisan serve

2. Duplicate ".env.example" and rename it to ".env"

3. Application Key (if not exist)
   => php artisan key:generate

4. Create database
   => php artisan migrate
   => php artisan migrate:fresh --seed

5.Update composer
   => composer update

------------------------KIT------------------------
Route::get($uri, $callback) - nhận resquest với phương thức GET.
Route::post($uri, $callback) - nhận resquest với phương thức POST.
=> Route::get('welcome', function () {
return "Xin chào";
});
