
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

try {
    require('./pages/' + route + '.js');
} catch (e) {
    if (e instanceof Error && e.code !== "MODULE_NOT_FOUND") {
        throw e;
    }
}
