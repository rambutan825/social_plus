// jQuery and Bootstrap-SASS
// -------------------------
// Questions: Why use CommonJS require?
// Answer: Because es6 module export lead to jquery plug-in can not run.
// -------------------------
window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

// The TS+ app.
import Vue from 'vue';
import App from './component/App';

/* eslint-disable no-new */
new Vue({
  el: '#app',
  render: h => h(App)
});
