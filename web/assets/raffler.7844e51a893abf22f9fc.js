/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/assets/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./source/js/raffler.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./source/js/raffler.js":
/*!******************************!*\
  !*** ./source/js/raffler.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * Main object.
 */
var Raffler = {
  /**
   * The delay between highlighting each checkin.
   */
  highlightDelay: 0,

  /**
   * The current hightlight cycle.
   */
  currentCycle: 0,

  /**
   * Array of truely random numbers. This is populated
   * on page load. We select the winners from this array.
   */
  winners: [],

  /**
   * Current state we are in. One of "start", "raffling", "winner"
   */
  state: 'start',

  /**
   * Initialize
   */
  init: function init(winners) {
    Raffler.winners = winners;
    $(document).on('keydown', Raffler.onKeyDown);
  },

  /**
   * On key down handler.
   */
  onKeyDown: function onKeyDown(e) {
    // We only handle the space (32) and page down (34) keys. Page down is enabled because
    // presentation remotes emit page down on the "next" button
    if (e.keyCode != 32 && e.keyCode != 34) {
      return;
    } // If we are in start state, start the raffler


    if (Raffler.state == 'start') {
      Raffler.raffle();
    } // If we are raffling, do nothing.


    if (Raffler.state == 'raffling') {
      return;
    } // If we are showing the winner, reset the state


    if (Raffler.state == 'winner') {
      Raffler.resetRaffler();
    }
  },

  /**
   * Raffle.
   */
  raffle: function raffle() {
    if (Raffler.winners.length <= 0) {
      alert("We have gone over the initial draft, please refresh page.");
      window.history.go(0);
      return;
    } // Hide checkin link


    $('.checkin-link').hide();
    Raffler.state = 'raffling';
    Raffler.highlightRandomCheckin();
  },

  /**
   * Present a winner.
   */
  showWinner: function showWinner() {
    // Change state
    Raffler.state = 'winner'; // Hide all checkins

    $('.checkin').addClass('loser', 1000); // Show winner

    var winner = $('.checkin').eq(Raffler.winners.pop());
    winner.switchClass('loser', 'winner', 200);
  },

  /**
   * Reset raffler
   */
  resetRaffler: function resetRaffler() {
    // Reset cycles and delay
    Raffler.currentCycle = 0;
    Raffler.highlightDelay = 0; // Reset styles

    $('.checkin').removeClass('loser');
    $('.checkin').removeClass('winner'); // Reset state

    Raffler.state = 'start';
  },

  /**
   * Highlight random checkin.
   */
  highlightRandomCheckin: function highlightRandomCheckin() {
    // Abort if we have reached 50 cycles
    if (45 <= Raffler.currentCycle) {
      Raffler.showWinner();
      return;
    } // Increase the current highlight cycle


    Raffler.currentCycle++; // Adjust the highlight delay

    Raffler.highlightDelay = Math.pow(1.14, Raffler.currentCycle); // Get random person to highlight

    checkin = Raffler.getRandomCheckin(); // Highlight, delay, unhighlight

    checkin.addClass('selected', Raffler.highlightDelay, Raffler.unhighlightCurrentCheckin);
  },

  /**
   * Unhighlight current checkin
   */
  unhighlightCurrentCheckin: function unhighlightCurrentCheckin() {
    // Unhighlight this checkin and recurse back to highlighting another
    // random checkin
    $(this).removeClass('selected', Raffler.highlightDelay, Raffler.highlightRandomCheckin);
  },

  /**
   * Get random checkin.
   */
  getRandomCheckin: function getRandomCheckin() {
    var random = Math.floor(Math.random() * $('.checkin').size());
    return $('.checkin').eq(random);
  }
};
var $checkins = $('.checkins[data-winners]');

if ($checkins.length > 0) {
  Raffler.init($checkins.data('winners').split(','));
}

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vc291cmNlL2pzL3JhZmZsZXIuanMiXSwibmFtZXMiOlsiUmFmZmxlciIsImhpZ2hsaWdodERlbGF5IiwiY3VycmVudEN5Y2xlIiwid2lubmVycyIsInN0YXRlIiwiaW5pdCIsIiQiLCJkb2N1bWVudCIsIm9uIiwib25LZXlEb3duIiwiZSIsImtleUNvZGUiLCJyYWZmbGUiLCJyZXNldFJhZmZsZXIiLCJsZW5ndGgiLCJhbGVydCIsIndpbmRvdyIsImhpc3RvcnkiLCJnbyIsImhpZGUiLCJoaWdobGlnaHRSYW5kb21DaGVja2luIiwic2hvd1dpbm5lciIsImFkZENsYXNzIiwid2lubmVyIiwiZXEiLCJwb3AiLCJzd2l0Y2hDbGFzcyIsInJlbW92ZUNsYXNzIiwiTWF0aCIsInBvdyIsImNoZWNraW4iLCJnZXRSYW5kb21DaGVja2luIiwidW5oaWdobGlnaHRDdXJyZW50Q2hlY2tpbiIsInJhbmRvbSIsImZsb29yIiwic2l6ZSIsIiRjaGVja2lucyIsImRhdGEiLCJzcGxpdCJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7O0FBR0E7QUFDQTs7Ozs7Ozs7Ozs7O0FDbEZBOzs7QUFHQSxJQUFJQSxPQUFPLEdBQUc7QUFDVjs7O0FBR0FDLGdCQUFjLEVBQUUsQ0FKTjs7QUFNVjs7O0FBR0FDLGNBQVksRUFBRSxDQVRKOztBQVdWOzs7O0FBSUFDLFNBQU8sRUFBRSxFQWZDOztBQWlCVjs7O0FBR0FDLE9BQUssRUFBRSxPQXBCRzs7QUFzQlY7OztBQUdBQyxNQUFJLEVBQUUsY0FBU0YsT0FBVCxFQUFrQjtBQUNwQkgsV0FBTyxDQUFDRyxPQUFSLEdBQWtCQSxPQUFsQjtBQUNBRyxLQUFDLENBQUNDLFFBQUQsQ0FBRCxDQUFZQyxFQUFaLENBQWUsU0FBZixFQUEwQlIsT0FBTyxDQUFDUyxTQUFsQztBQUNILEdBNUJTOztBQThCVjs7O0FBR0FBLFdBQVMsRUFBRSxtQkFBU0MsQ0FBVCxFQUFZO0FBQ25CO0FBQ0E7QUFDQSxRQUFJQSxDQUFDLENBQUNDLE9BQUYsSUFBYSxFQUFiLElBQW1CRCxDQUFDLENBQUNDLE9BQUYsSUFBYSxFQUFwQyxFQUF3QztBQUNwQztBQUNILEtBTGtCLENBT25COzs7QUFDQSxRQUFJWCxPQUFPLENBQUNJLEtBQVIsSUFBaUIsT0FBckIsRUFBOEI7QUFDMUJKLGFBQU8sQ0FBQ1ksTUFBUjtBQUNILEtBVmtCLENBWW5COzs7QUFDQSxRQUFJWixPQUFPLENBQUNJLEtBQVIsSUFBaUIsVUFBckIsRUFBaUM7QUFDN0I7QUFDSCxLQWZrQixDQWlCbkI7OztBQUNBLFFBQUlKLE9BQU8sQ0FBQ0ksS0FBUixJQUFpQixRQUFyQixFQUErQjtBQUMzQkosYUFBTyxDQUFDYSxZQUFSO0FBQ0g7QUFDSixHQXREUzs7QUF3RFY7OztBQUdBRCxRQUFNLEVBQUUsa0JBQVk7QUFFaEIsUUFBSVosT0FBTyxDQUFDRyxPQUFSLENBQWdCVyxNQUFoQixJQUEwQixDQUE5QixFQUFrQztBQUM5QkMsV0FBSyxDQUFDLDJEQUFELENBQUw7QUFDQUMsWUFBTSxDQUFDQyxPQUFQLENBQWVDLEVBQWYsQ0FBa0IsQ0FBbEI7QUFDQTtBQUNILEtBTmUsQ0FRaEI7OztBQUNBWixLQUFDLENBQUMsZUFBRCxDQUFELENBQW1CYSxJQUFuQjtBQUVBbkIsV0FBTyxDQUFDSSxLQUFSLEdBQWdCLFVBQWhCO0FBQ0FKLFdBQU8sQ0FBQ29CLHNCQUFSO0FBQ0gsR0F4RVM7O0FBMEVWOzs7QUFHQUMsWUFBVSxFQUFFLHNCQUFXO0FBQ25CO0FBQ0FyQixXQUFPLENBQUNJLEtBQVIsR0FBZ0IsUUFBaEIsQ0FGbUIsQ0FJbkI7O0FBQ0FFLEtBQUMsQ0FBQyxVQUFELENBQUQsQ0FBY2dCLFFBQWQsQ0FBdUIsT0FBdkIsRUFBZ0MsSUFBaEMsRUFMbUIsQ0FRbkI7O0FBQ0EsUUFBSUMsTUFBTSxHQUFHakIsQ0FBQyxDQUFDLFVBQUQsQ0FBRCxDQUFja0IsRUFBZCxDQUFpQnhCLE9BQU8sQ0FBQ0csT0FBUixDQUFnQnNCLEdBQWhCLEVBQWpCLENBQWI7QUFDQUYsVUFBTSxDQUFDRyxXQUFQLENBQW1CLE9BQW5CLEVBQTRCLFFBQTVCLEVBQXNDLEdBQXRDO0FBQ0gsR0F4RlM7O0FBMEZWOzs7QUFHQWIsY0FBWSxFQUFFLHdCQUFXO0FBQ3JCO0FBQ0FiLFdBQU8sQ0FBQ0UsWUFBUixHQUF1QixDQUF2QjtBQUNBRixXQUFPLENBQUNDLGNBQVIsR0FBeUIsQ0FBekIsQ0FIcUIsQ0FLckI7O0FBQ0FLLEtBQUMsQ0FBQyxVQUFELENBQUQsQ0FBY3FCLFdBQWQsQ0FBMEIsT0FBMUI7QUFDQXJCLEtBQUMsQ0FBQyxVQUFELENBQUQsQ0FBY3FCLFdBQWQsQ0FBMEIsUUFBMUIsRUFQcUIsQ0FTckI7O0FBQ0EzQixXQUFPLENBQUNJLEtBQVIsR0FBZ0IsT0FBaEI7QUFDSCxHQXhHUzs7QUEwR1Y7OztBQUdBZ0Isd0JBQXNCLEVBQUUsa0NBQVc7QUFDL0I7QUFDQSxRQUFJLE1BQU1wQixPQUFPLENBQUNFLFlBQWxCLEVBQWdDO0FBQzVCRixhQUFPLENBQUNxQixVQUFSO0FBQ0E7QUFDSCxLQUw4QixDQU8vQjs7O0FBQ0FyQixXQUFPLENBQUNFLFlBQVIsR0FSK0IsQ0FVL0I7O0FBQ0FGLFdBQU8sQ0FBQ0MsY0FBUixHQUF5QjJCLElBQUksQ0FBQ0MsR0FBTCxDQUFTLElBQVQsRUFBZTdCLE9BQU8sQ0FBQ0UsWUFBdkIsQ0FBekIsQ0FYK0IsQ0FhL0I7O0FBQ0E0QixXQUFPLEdBQUc5QixPQUFPLENBQUMrQixnQkFBUixFQUFWLENBZCtCLENBZ0IvQjs7QUFDQUQsV0FBTyxDQUFDUixRQUFSLENBQWlCLFVBQWpCLEVBQTZCdEIsT0FBTyxDQUFDQyxjQUFyQyxFQUFxREQsT0FBTyxDQUFDZ0MseUJBQTdEO0FBQ0gsR0EvSFM7O0FBaUlWOzs7QUFHQUEsMkJBQXlCLEVBQUUscUNBQVc7QUFDbEM7QUFDQTtBQUNBMUIsS0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRcUIsV0FBUixDQUFvQixVQUFwQixFQUFnQzNCLE9BQU8sQ0FBQ0MsY0FBeEMsRUFBd0RELE9BQU8sQ0FBQ29CLHNCQUFoRTtBQUNILEdBeElTOztBQTBJVjs7O0FBR0FXLGtCQUFnQixFQUFFLDRCQUFXO0FBQ3pCLFFBQUlFLE1BQU0sR0FBR0wsSUFBSSxDQUFDTSxLQUFMLENBQVdOLElBQUksQ0FBQ0ssTUFBTCxLQUFnQjNCLENBQUMsQ0FBQyxVQUFELENBQUQsQ0FBYzZCLElBQWQsRUFBM0IsQ0FBYjtBQUNBLFdBQU83QixDQUFDLENBQUMsVUFBRCxDQUFELENBQWNrQixFQUFkLENBQWlCUyxNQUFqQixDQUFQO0FBQ0g7QUFoSlMsQ0FBZDtBQW1KQSxJQUFJRyxTQUFTLEdBQUc5QixDQUFDLENBQUMseUJBQUQsQ0FBakI7O0FBQ0EsSUFBSThCLFNBQVMsQ0FBQ3RCLE1BQVYsR0FBbUIsQ0FBdkIsRUFBMEI7QUFDdEJkLFNBQU8sQ0FBQ0ssSUFBUixDQUFhK0IsU0FBUyxDQUFDQyxJQUFWLENBQWUsU0FBZixFQUEwQkMsS0FBMUIsQ0FBZ0MsR0FBaEMsQ0FBYjtBQUNILEMiLCJmaWxlIjoicmFmZmxlci43ODQ0ZTUxYTg5M2FiZjIyZjlmYy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiL2Fzc2V0cy9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9zb3VyY2UvanMvcmFmZmxlci5qc1wiKTtcbiIsIi8qKlxuICogTWFpbiBvYmplY3QuXG4gKi9cbnZhciBSYWZmbGVyID0ge1xuICAgIC8qKlxuICAgICAqIFRoZSBkZWxheSBiZXR3ZWVuIGhpZ2hsaWdodGluZyBlYWNoIGNoZWNraW4uXG4gICAgICovXG4gICAgaGlnaGxpZ2h0RGVsYXk6IDAsXG5cbiAgICAvKipcbiAgICAgKiBUaGUgY3VycmVudCBoaWdodGxpZ2h0IGN5Y2xlLlxuICAgICAqL1xuICAgIGN1cnJlbnRDeWNsZTogMCxcblxuICAgIC8qKlxuICAgICAqIEFycmF5IG9mIHRydWVseSByYW5kb20gbnVtYmVycy4gVGhpcyBpcyBwb3B1bGF0ZWRcbiAgICAgKiBvbiBwYWdlIGxvYWQuIFdlIHNlbGVjdCB0aGUgd2lubmVycyBmcm9tIHRoaXMgYXJyYXkuXG4gICAgICovXG4gICAgd2lubmVyczogW10sXG5cbiAgICAvKipcbiAgICAgKiBDdXJyZW50IHN0YXRlIHdlIGFyZSBpbi4gT25lIG9mIFwic3RhcnRcIiwgXCJyYWZmbGluZ1wiLCBcIndpbm5lclwiXG4gICAgICovXG4gICAgc3RhdGU6ICdzdGFydCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplXG4gICAgICovXG4gICAgaW5pdDogZnVuY3Rpb24od2lubmVycykge1xuICAgICAgICBSYWZmbGVyLndpbm5lcnMgPSB3aW5uZXJzO1xuICAgICAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsIFJhZmZsZXIub25LZXlEb3duKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogT24ga2V5IGRvd24gaGFuZGxlci5cbiAgICAgKi9cbiAgICBvbktleURvd246IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgLy8gV2Ugb25seSBoYW5kbGUgdGhlIHNwYWNlICgzMikgYW5kIHBhZ2UgZG93biAoMzQpIGtleXMuIFBhZ2UgZG93biBpcyBlbmFibGVkIGJlY2F1c2VcbiAgICAgICAgLy8gcHJlc2VudGF0aW9uIHJlbW90ZXMgZW1pdCBwYWdlIGRvd24gb24gdGhlIFwibmV4dFwiIGJ1dHRvblxuICAgICAgICBpZiAoZS5rZXlDb2RlICE9IDMyICYmIGUua2V5Q29kZSAhPSAzNCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgd2UgYXJlIGluIHN0YXJ0IHN0YXRlLCBzdGFydCB0aGUgcmFmZmxlclxuICAgICAgICBpZiAoUmFmZmxlci5zdGF0ZSA9PSAnc3RhcnQnKSB7XG4gICAgICAgICAgICBSYWZmbGVyLnJhZmZsZSgpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgd2UgYXJlIHJhZmZsaW5nLCBkbyBub3RoaW5nLlxuICAgICAgICBpZiAoUmFmZmxlci5zdGF0ZSA9PSAncmFmZmxpbmcnKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBJZiB3ZSBhcmUgc2hvd2luZyB0aGUgd2lubmVyLCByZXNldCB0aGUgc3RhdGVcbiAgICAgICAgaWYgKFJhZmZsZXIuc3RhdGUgPT0gJ3dpbm5lcicpIHtcbiAgICAgICAgICAgIFJhZmZsZXIucmVzZXRSYWZmbGVyKCk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmFmZmxlLlxuICAgICAqL1xuICAgIHJhZmZsZTogZnVuY3Rpb24gKCkge1xuXG4gICAgICAgIGlmIChSYWZmbGVyLndpbm5lcnMubGVuZ3RoIDw9IDAgKSB7XG4gICAgICAgICAgICBhbGVydChcIldlIGhhdmUgZ29uZSBvdmVyIHRoZSBpbml0aWFsIGRyYWZ0LCBwbGVhc2UgcmVmcmVzaCBwYWdlLlwiKTtcbiAgICAgICAgICAgIHdpbmRvdy5oaXN0b3J5LmdvKDApO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSGlkZSBjaGVja2luIGxpbmtcbiAgICAgICAgJCgnLmNoZWNraW4tbGluaycpLmhpZGUoKTtcblxuICAgICAgICBSYWZmbGVyLnN0YXRlID0gJ3JhZmZsaW5nJztcbiAgICAgICAgUmFmZmxlci5oaWdobGlnaHRSYW5kb21DaGVja2luKCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFByZXNlbnQgYSB3aW5uZXIuXG4gICAgICovXG4gICAgc2hvd1dpbm5lcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIC8vIENoYW5nZSBzdGF0ZVxuICAgICAgICBSYWZmbGVyLnN0YXRlID0gJ3dpbm5lcic7XG5cbiAgICAgICAgLy8gSGlkZSBhbGwgY2hlY2tpbnNcbiAgICAgICAgJCgnLmNoZWNraW4nKS5hZGRDbGFzcygnbG9zZXInLCAxMDAwKTtcblxuXG4gICAgICAgIC8vIFNob3cgd2lubmVyXG4gICAgICAgIHZhciB3aW5uZXIgPSAkKCcuY2hlY2tpbicpLmVxKFJhZmZsZXIud2lubmVycy5wb3AoKSk7XG4gICAgICAgIHdpbm5lci5zd2l0Y2hDbGFzcygnbG9zZXInLCAnd2lubmVyJywgMjAwKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVzZXQgcmFmZmxlclxuICAgICAqL1xuICAgIHJlc2V0UmFmZmxlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIC8vIFJlc2V0IGN5Y2xlcyBhbmQgZGVsYXlcbiAgICAgICAgUmFmZmxlci5jdXJyZW50Q3ljbGUgPSAwO1xuICAgICAgICBSYWZmbGVyLmhpZ2hsaWdodERlbGF5ID0gMDtcblxuICAgICAgICAvLyBSZXNldCBzdHlsZXNcbiAgICAgICAgJCgnLmNoZWNraW4nKS5yZW1vdmVDbGFzcygnbG9zZXInKTtcbiAgICAgICAgJCgnLmNoZWNraW4nKS5yZW1vdmVDbGFzcygnd2lubmVyJyk7XG5cbiAgICAgICAgLy8gUmVzZXQgc3RhdGVcbiAgICAgICAgUmFmZmxlci5zdGF0ZSA9ICdzdGFydCc7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEhpZ2hsaWdodCByYW5kb20gY2hlY2tpbi5cbiAgICAgKi9cbiAgICBoaWdobGlnaHRSYW5kb21DaGVja2luOiBmdW5jdGlvbigpIHtcbiAgICAgICAgLy8gQWJvcnQgaWYgd2UgaGF2ZSByZWFjaGVkIDUwIGN5Y2xlc1xuICAgICAgICBpZiAoNDUgPD0gUmFmZmxlci5jdXJyZW50Q3ljbGUpIHtcbiAgICAgICAgICAgIFJhZmZsZXIuc2hvd1dpbm5lcigpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSW5jcmVhc2UgdGhlIGN1cnJlbnQgaGlnaGxpZ2h0IGN5Y2xlXG4gICAgICAgIFJhZmZsZXIuY3VycmVudEN5Y2xlKys7XG5cbiAgICAgICAgLy8gQWRqdXN0IHRoZSBoaWdobGlnaHQgZGVsYXlcbiAgICAgICAgUmFmZmxlci5oaWdobGlnaHREZWxheSA9IE1hdGgucG93KDEuMTQsIFJhZmZsZXIuY3VycmVudEN5Y2xlKTtcblxuICAgICAgICAvLyBHZXQgcmFuZG9tIHBlcnNvbiB0byBoaWdobGlnaHRcbiAgICAgICAgY2hlY2tpbiA9IFJhZmZsZXIuZ2V0UmFuZG9tQ2hlY2tpbigpO1xuXG4gICAgICAgIC8vIEhpZ2hsaWdodCwgZGVsYXksIHVuaGlnaGxpZ2h0XG4gICAgICAgIGNoZWNraW4uYWRkQ2xhc3MoJ3NlbGVjdGVkJywgUmFmZmxlci5oaWdobGlnaHREZWxheSwgUmFmZmxlci51bmhpZ2hsaWdodEN1cnJlbnRDaGVja2luKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogVW5oaWdobGlnaHQgY3VycmVudCBjaGVja2luXG4gICAgICovXG4gICAgdW5oaWdobGlnaHRDdXJyZW50Q2hlY2tpbjogZnVuY3Rpb24oKSB7XG4gICAgICAgIC8vIFVuaGlnaGxpZ2h0IHRoaXMgY2hlY2tpbiBhbmQgcmVjdXJzZSBiYWNrIHRvIGhpZ2hsaWdodGluZyBhbm90aGVyXG4gICAgICAgIC8vIHJhbmRvbSBjaGVja2luXG4gICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ3NlbGVjdGVkJywgUmFmZmxlci5oaWdobGlnaHREZWxheSwgUmFmZmxlci5oaWdobGlnaHRSYW5kb21DaGVja2luKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogR2V0IHJhbmRvbSBjaGVja2luLlxuICAgICAqL1xuICAgIGdldFJhbmRvbUNoZWNraW46IGZ1bmN0aW9uKCkge1xuICAgICAgICB2YXIgcmFuZG9tID0gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogJCgnLmNoZWNraW4nKS5zaXplKCkpO1xuICAgICAgICByZXR1cm4oJCgnLmNoZWNraW4nKS5lcShyYW5kb20pKTtcbiAgICB9XG59O1xuXG5sZXQgJGNoZWNraW5zID0gJCgnLmNoZWNraW5zW2RhdGEtd2lubmVyc10nKTtcbmlmICgkY2hlY2tpbnMubGVuZ3RoID4gMCkge1xuICAgIFJhZmZsZXIuaW5pdCgkY2hlY2tpbnMuZGF0YSgnd2lubmVycycpLnNwbGl0KCcsJykpO1xufVxuIl0sInNvdXJjZVJvb3QiOiIifQ==