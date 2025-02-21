<?php

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Brackets\AdminAuth\Models\AdminUser::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt($faker->password),
        'remember_token' => null,
        'activated' => true,
        'forbidden' => $faker->boolean(),
        'language' => 'en',
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'last_login_at' => $faker->dateTime,
        
    ];
});/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Modality::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Land::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'short_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Document::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'status' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Category::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ProjectType::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'short_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Stage::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Typology::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Parentesco::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Discapacidad::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ModalityHasLand::class, static function (Faker\Generator $faker) {
    return [
        'modality_id' => $faker->sentence,
        'land_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LandHasProjectType::class, static function (Faker\Generator $faker) {
    return [
        'land_id' => $faker->sentence,
        'project_type_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Assignment::class, static function (Faker\Generator $faker) {
    return [
        'document_id' => $faker->sentence,
        'category_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'project_type_id' => $faker->sentence,
        'stage_id' => $faker->randomNumber(5),
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ProjectTypeHasTypology::class, static function (Faker\Generator $faker) {
    return [
        'project_type_id' => $faker->sentence,
        'typology_id' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'email' => $faker->email,
        'username' => $faker->sentence,
        'password' => bcrypt($faker->password),
        'remember_token' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'sat_ruc' => $faker->sentence,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Project::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'phone' => $faker->sentence,
        'sat_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'city_id' => $faker->sentence,
        'modalidad_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'leader_name' => $faker->sentence,
        'localidad' => $faker->sentence,
        'land_id' => $faker->sentence,
        'typology_id' => $faker->randomNumber(5),
        'action' => $faker->sentence,
        'expsocial' => $faker->sentence,
        'exptecnico' => $faker->sentence,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DocumentCheck::class, static function (Faker\Generator $faker) {
    return [
        'project_id' => $faker->randomNumber(5),
        'document_id' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ProjectStatus::class, static function (Faker\Generator $faker) {
    return [
        'project_id' => $faker->sentence,
        'stage_id' => $faker->randomNumber(5),
        'user_id' => $faker->randomNumber(5),
        'record' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Postulante::class, static function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'cedula' => $faker->sentence,
        'marital_status' => $faker->sentence,
        'nacionalidad' => $faker->sentence,
        'gender' => $faker->sentence,
        'birthdate' => $faker->sentence,
        'localidad' => $faker->sentence,
        'asentamiento' => $faker->sentence,
        'ingreso' => $faker->sentence,
        'address' => $faker->sentence,
        'grupo' => $faker->sentence,
        'phone' => $faker->sentence,
        'mobile' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'nexp' => $faker->sentence,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Comentario::class, static function (Faker\Generator $faker) {
    return [
        'postulante_id' => $faker->sentence,
        'cedula' => $faker->sentence,
        'comentario' => $faker->text(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Motivo::class, static function (Faker\Generator $faker) {
    return [
        'project_id' => $faker->sentence,
        'motivo' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AdminUsersDependency::class, static function (Faker\Generator $faker) {
    return [
        'admin_user_id' => $faker->randomNumber(5),
        'dependency_id' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Dependency::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Reporte::class, static function (Faker\Generator $faker) {
    return [
        'inicio' => $faker->dateTime,
        'fin' => $faker->dateTime,
        'sat_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'city_id' => $faker->sentence,
        'modalidad_id' => $faker->sentence,
        'stage_id' => $faker->sentence,
        
        
    ];
});
