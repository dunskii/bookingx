// requiere moment.js
// requiere bootstrap
// requiere jquery
// requiere calendar.css
// autor: Yordanch Vargas Velasque
// email: snd.yvv@gmail.com

moment.locale( "en" );

class CalendarYvv {
	constructor(etiqueta = "", diaSeleccionado = "", primerDia = "Lunes") {
		this.etiqueta        = etiqueta; // etiqueta donde se mostrará
		this.primerDia       = primerDia; // inicio de la semana
		this.diaSeleccionado = diaSeleccionado == "" ? moment().format( "Y-M-D" ) : diaSeleccionado; // día actual seleccionado

		this.funcPer                     = function (e) {
		}; // funcion a ejecutar al lanzar el evento click
		this.funcNext                    = false; // funcion a ejecutar al lanzar el evento click
		this.funcPrev                    = false; // funcion a ejecutar al lanzar el evento click
		this.currentSelected             = moment().format( "Y-M-D" ); // elemento seleccionado
		this.staffAvailableCertainMonths = []; // Resource only be available certain months of the year
		this.staffAvailableCertainDays   = []; // Resource be available at certain days only
		this.unavailable_days            = []; // Resource be available at certain days only
		this.diasResal                   = []; // dias importantes
		this.colorResal                  = "#ebebeb"; // Color of important days
		this.textResalt                  = "#fff"; // Preload Selected Days Color

		this.bg         = "bg-light"; // color de fondo de la cabecera
		this.textColor  = "text-dark"; // color de texto en la cabecera
		this.btnH       = "btn-outline-light"; // color de boton normal
		this.btnD       = "btn-rounded-success bkx-cal-enable";// color de boton al pasar el mouse - "btn-outline-dark";
		this.btnDisable = "bkx-cal-disable";
	}

	startElements() {
		this.diaSeleccionado  = this.corregirMesA( this.diaSeleccionado );
		this.inicioDia        = moment( this.diaSeleccionado ).format( "DDDD" ); // start day of the month
		this.mesSeleccionado  = this.diaSeleccionado.split( "-" )[1]; // selected month
		this.anioSeleccionado = this.diaSeleccionado.split( "-" )[0] * 1; // selected year
		this.cantDias         = moment( this.diaSeleccionado ).daysInMonth(); // number of days of the month
		this.diasCoto         = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
		this.diasLargo        = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
	}

	createCalendar() {
		var $ = jQuery.noConflict();
		this.startElements();
		var cont             = $( this.etiqueta );
		var cntCale          = $( "<div class='calendar-yvv w-100'>" );
		var headerCalendar   = this.createHeaderM();
		var daysLettCalendar = this.createDayTag();
		var daysNumCalendar  = this.createDaysMont();

		cont.html( "" );
		cntCale.append( headerCalendar );
		cntCale.append( daysLettCalendar );
		cntCale.append( daysNumCalendar );
		cont.append( cntCale );
	}

	createHeaderM() {
		var $      = jQuery.noConflict();
		var cont   = $( "<div class='d-flex justify-content-between align-items-center calendar-month " + this.bg + " " + this.textColor + "'>" );
		var arrowL = $( "<span class='btn " + this.btnH + "'>" ).html( "<i class='la la-angle-left'></i>" );
		var arrowR = $( "<span class='btn " + this.btnH + "'>" ).html( "<i class='la la-angle-right'></i>" );
		var title  = $( "<span class='text-uppercase'>" ).html( moment( this.diaSeleccionado ).format( "MMMM - Y" ) );
		var _this  = this;

		arrowL.on(
			"click",
			function (e) {
				var dtPrev = new Date();
				var day    = _this.diaSeleccionado.split( "-" )[2] * 1;
				dtPrev.setFullYear( _this.anioSeleccionado, _this.mesSeleccionado - 1, day );
				var dtToday = new Date();
				// console.log(dtPrev +"===="+ dtToday)
				if (dtPrev < dtToday) {
				} else {
					_this.mesAnterior( _this ) // Previous Month
				}
			}
		);
		arrowR.on(
			"click",
			function (e) {
				_this.mesSiguiente( _this ) // Next Month
			}
		);
		cont.append( arrowL );
		cont.append( title );
		cont.append( arrowR );
		return cont;
	}

	createDayTag() {
		var $            = jQuery.noConflict();
		var newPrimerDia = this.firtsMayus( this.primerDia );
		var diasOrd      = this.ordenarDiasMes( newPrimerDia );

		var cont = $( "<div class='d-flex w-100 calendar-week " + this.bg + " " + this.textColor + "'>" );

		diasOrd.fechCort.forEach(
			function (e) {
				var div = $( "<div class='d-flex flex-fill w-100 justify-content-center p-2'>" ).html( e );
				cont.append( div );
			}
		);
		return cont;
	}

	createDaysMont() {
		var $            = jQuery.noConflict();
		var diaSelected  = this.corregirMesA( this.anioSeleccionado + "-" + this.mesSeleccionado + "-01" );
		var primerDiaMes = moment( diaSelected ).format( "dddd" );
		var diaInicio    = this.firtsMayus( primerDiaMes ); // this.firtsMayus(this.inicioDia);
		var diasOrd      = this.ordenarDiasMes( this.firtsMayus( this.primerDia ) );
		var posMes       = diasOrd.fechLarg.indexOf( diaInicio );

		var cnt  = 0;
		var cntG = $( "<div class='w-100 calendar-day'>" );

		var cont     = $( "<div class='d-flex w-100'>" );
		var emptyTag = "<div class='d-flex flex-fill w-100 justify-content-center btn calendar-btn' style='color:transparent'>";
		for (var j = 0; j < posMes; j++) {
			var div = $( emptyTag ).html( "0" );
			cont.append( div );
			cnt++;
		}
		var current_date_selected = "";
		for (var i = 0; i < this.cantDias; i++) {

			var fechNow = this.anioSeleccionado + "-" + this.mesSeleccionado + "-" + (i + 1);
			// dia seleccionado
			if (this.diaSeleccionado == moment( fechNow ).format( "Y-MM-DD" )) {
				current_date_selected = ' current-date-selected ';
			}
			var div           = $( "<div class='d-flex flex-fill w-100 justify-content-center btn " + this.btnD + "' data-date='" + fechNow + "'>" ).html( i + 1 );
			var clas_e        = this;
			var _ind          = (this.cantDias + posMes) % 7;
			var current_month = moment( this.diaSeleccionado ).format( "MMMM" );
			var current_day   = moment( fechNow ).format( "dddd" );

			if (this.diaSeleccionado == moment( fechNow ).format( "Y-MM-DD" )) {
				div = $( "<div class='current-date-selected d-flex flex-fill w-100 justify-content-center btn " + this.btnD + "' data-date='" + fechNow + "'>" ).html( i + 1 );
			}

			// Start Less than today date will be disable
			if (moment( fechNow ).format( "Y-MM-DD" ) < moment().format( "Y-MM-DD" )) {
				div = $( "<div class='d-flex flex-fill w-100 justify-content-center btn " + this.btnDisable + "' data-date='" + fechNow + "' style='background: " + this.colorResal + "; color: " + this.textResalt + "; font-weight: bold;'>" ).html( i + 1 );
			}
			// End Less than today date will be disable

			if (this.staffAvailableCertainMonths && this.staffAvailableCertainMonths.length > 0) {
				if (this.staffAvailableCertainMonths.indexOf( current_month ) > -1) {
					// div = $("<div class='" + current_date_selected + "d-flex flex-fill w-100 justify-content-center btn " + this.btnD + "' data-date='" + fechNow + "'>").html(i + 1);
				} else {
					div = $( "<div class='d-flex flex-fill w-100 justify-content-center btn " + this.btnDisable + "' data-date='" + fechNow + "' style='background: " + this.colorResal + "; color: " + this.textResalt + "; font-weight: bold;'>" ).html( i + 1 );
				}
			}

			if (this.staffAvailableCertainDays && this.staffAvailableCertainDays.length > 0) {
				if (this.staffAvailableCertainDays.indexOf( current_day ) > -1) {
					// div = $("<div class='" + current_date_selected + "d-flex flex-fill w-100 justify-content-center btn " + this.btnD + "' data-date='" + fechNow + "'>").html(i + 1);
				} else {
					div = $( "<div class='d-flex flex-fill w-100 justify-content-center btn " + this.btnDisable + "' data-date='" + fechNow + "' style='background: " + this.colorResal + "; color: " + this.textResalt + "; font-weight: bold;'>" ).html( i + 1 );
				}
			}

			// dias resaltados o importantes
			if (this.diasResal.indexOf( i + 1 ) != -1) {
				div = $( "<div class='d-flex flex-fill w-100 justify-content-center btn " + this.btnDisable + "' data-date='" + fechNow + "' style='background: " + this.colorResal + "; color: " + this.textResalt + "; font-weight: bold;'>" ).html( i + 1 );
			}

			// Unavailable Days
			if (this.unavailable_days && this.unavailable_days.indexOf( moment( fechNow ).format( "MM/DD/Y" ) ) != -1) {
				div = $( "<div class='d-flex flex-fill w-100 justify-content-center btn " + this.btnDisable + "' data-date='" + fechNow + "' style='background: " + this.colorResal + "; color: " + this.textResalt + "; font-weight: bold;'>" ).html( i + 1 );
			}

			div.on(
				"click",
				function (e) {
					if ($( e.target ).hasClass( "bkx-cal-enable" )) {
						var daySelec = $( e.target ).attr( "data-date" );

						$( '.calendar-day' ).find( '.on-click-selected' ).each(
							function (index, value) {
								$( this ).removeClass( 'on-click-selected' );
							}
						);
						$( '.calendar-day' ).find( '.day-click-selected' ).each(
							function (index, value) {
								$( this ).removeClass( 'day-click-selected' );
							}
						);
						$( e.target ).addClass( 'on-click-selected' );
						clas_e.currentSelected = daySelec;
						clas_e.funcPer( clas_e )
					}

				}
			);
			cont.append( div );
			if (cnt == 6) {
				// div.on("click", this.funcPer);
				cntG.append( cont );
				cont = $( "<div class='d-flex w-100'>" );
				cnt  = 0;
			} else if (this.cantDias == (i + 1)) {
				for (var j = 0; j < (7 - _ind); j++) {
					var div = $( emptyTag ).html( "0" );
					cont.append( div );
					cnt++;
				}
				cntG.append( cont );
				cont = $( "<div class='d-flex w-100 selected'>" );
				cnt  = 0;
			} else {
				// cont.append(div);
				cnt++;
			}
		}
		return cntG;
	}

	CertainMonths() {

	}

	ordenarDiasMes(dia) {
		var $        = jQuery.noConflict();
		var posMes   = this.diasLargo.indexOf( dia );
		var fechCort = [];
		var fechLarg = [];

		for (var i = posMes; i < this.diasCoto.length; i++) {
			fechCort.push( this.diasCoto[i] );
			fechLarg.push( this.diasLargo[i] );
		}
		for (var j = 0; j < posMes; j++) {
			fechCort.push( this.diasCoto[j] );
			fechLarg.push( this.diasLargo[j] );
		}
		return {fechCort, fechLarg};
	}

	firtsMayus(letter) {
		var lett = "";
		for (var i = 0; i < letter.length; i++) {
			if (i == 0) {
				lett += "" + letter[i].toUpperCase();
			} else {
				lett += "" + letter[i].toLowerCase();
			}
		}
		return lett;
	}

	mesAnterior(ev) {
		ev.mesSeleccionado--;
		if (ev.mesSeleccionado == 0) {
			ev.anioSeleccionado--;
			ev.mesSeleccionado = 12;
		}

		var day            = ev.diaSeleccionado.split( "-" )[2] * 1;
		ev.diaSeleccionado = ev.anioSeleccionado + "-" + ev.mesSeleccionado + "-" + day;

		ev.diaSeleccionado = ev.corregirMesA( ev.diaSeleccionado );
		ev.cantDias        = moment( ev.diaSeleccionado ).daysInMonth() * 1;
		ev.createCalendar();

		if (this.funcPrev) {
			this.funcPrev( ev )
		} else {
			ev.createCalendar();
		}
	}

	mesSiguiente(ev) {
		ev.mesSeleccionado++;
		if (ev.mesSeleccionado == 13) {
			ev.anioSeleccionado++;
			ev.mesSeleccionado = 1;
		}
		var day            = ev.diaSeleccionado.split( "-" )[2] * 1;
		ev.diaSeleccionado = ev.anioSeleccionado + "-" + ev.mesSeleccionado + "-" + day;
		ev.diaSeleccionado = ev.corregirMesA( ev.diaSeleccionado );
		ev.cantDias        = moment( ev.diaSeleccionado ).daysInMonth() * 1;

		if (this.funcNext) {
			this.funcNext( ev )
		} else {
			ev.createCalendar();
		}
	}

	corregirMesA(_f) {
		var fec = _f.split( "-" );
		var day = 1;
		fec[1]  = (fec[1] < 10 && fec[1].length == 1) ? ("0" + fec[1]) : fec[1];
		fec[2]  = (fec[2] < 10 && fec[2].length == 1) ? ("0" + fec[2]) : day;
		return moment( fec.join( "-" ) ).format( 'Y-M-D' );
	}
}
