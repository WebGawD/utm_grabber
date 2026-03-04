/**
 * Awsisa Watersan Dialogue 2026 — Main JavaScript
 *
 * Handles: countdown timer, agenda tabs, mobile nav toggle,
 * donation tier selection, smooth scroll, and NFC-link logging.
 */

( function () {
	'use strict';

	// ============================================================
	// Countdown Timer (target: 9 November 2026 08:00 SAST UTC+2)
	// ============================================================
	const countdownTarget = new Date( '2026-11-09T08:00:00+02:00' ).getTime();

	function updateCountdown() {
		const now  = Date.now();
		const diff = countdownTarget - now;

		const dEl = document.getElementById( 'countdown-days' );
		const hEl = document.getElementById( 'countdown-hours' );
		const mEl = document.getElementById( 'countdown-minutes' );
		const sEl = document.getElementById( 'countdown-seconds' );

		if ( ! dEl ) return;

		if ( diff <= 0 ) {
			dEl.textContent = '00';
			hEl.textContent = '00';
			mEl.textContent = '00';
			sEl.textContent = '00';
			return;
		}

		const days    = Math.floor( diff / ( 1000 * 60 * 60 * 24 ) );
		const hours   = Math.floor( ( diff % ( 1000 * 60 * 60 * 24 ) ) / ( 1000 * 60 * 60 ) );
		const minutes = Math.floor( ( diff % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 ) );
		const seconds = Math.floor( ( diff % ( 1000 * 60 ) ) / 1000 );

		dEl.textContent = String( days ).padStart( 2, '0' );
		hEl.textContent = String( hours ).padStart( 2, '0' );
		mEl.textContent = String( minutes ).padStart( 2, '0' );
		sEl.textContent = String( seconds ).padStart( 2, '0' );
	}

	if ( document.getElementById( 'countdown-days' ) ) {
		updateCountdown();
		setInterval( updateCountdown, 1000 );
	}

	// ============================================================
	// Mobile Navigation Toggle
	// ============================================================
	const navToggle = document.getElementById( 'nav-toggle' );
	const navMenu   = document.getElementById( 'nav-menu' );

	if ( navToggle && navMenu ) {
		navToggle.addEventListener( 'click', function () {
			const isOpen = navMenu.classList.toggle( 'is-open' );
			navToggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );

		// Close on outside click.
		document.addEventListener( 'click', function ( e ) {
			if ( ! navToggle.contains( e.target ) && ! navMenu.contains( e.target ) ) {
				navMenu.classList.remove( 'is-open' );
				navToggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	// ============================================================
	// Agenda Day Tabs
	// ============================================================
	const agendaTabs   = document.querySelectorAll( '.agenda-tab' );
	const agendaPanels = document.querySelectorAll( '.agenda-day-panel' );

	if ( agendaTabs.length > 0 ) {
		agendaTabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				const targetId = this.getAttribute( 'aria-controls' );

				// Update tab active states.
				agendaTabs.forEach( function ( t ) {
					t.classList.remove( 'is-active' );
					t.setAttribute( 'aria-selected', 'false' );
				} );
				this.classList.add( 'is-active' );
				this.setAttribute( 'aria-selected', 'true' );

				// Show the corresponding panel.
				agendaPanels.forEach( function ( panel ) {
					if ( panel.id === targetId ) {
						panel.removeAttribute( 'hidden' );
					} else {
						panel.setAttribute( 'hidden', '' );
					}
				} );
			} );
		} );
	}

	// ============================================================
	// Donation Tier Selection
	// ============================================================
	const donationTiers = document.querySelectorAll( '.donation-tier' );

	if ( donationTiers.length > 0 ) {
		donationTiers.forEach( function ( tier ) {
			tier.addEventListener( 'click', function () {
				donationTiers.forEach( function ( t ) {
					t.classList.remove( 'is-selected' );
					t.setAttribute( 'aria-pressed', 'false' );
				} );
				this.classList.add( 'is-selected' );
				this.setAttribute( 'aria-pressed', 'true' );

				const amount = this.dataset.amount;
				const customInput = document.getElementById( 'custom-amount' );
				if ( amount && customInput ) {
					customInput.value = amount;
				}
			} );
		} );
	}

	// ============================================================
	// Sticky Header Shadow on Scroll
	// ============================================================
	const siteHeader = document.getElementById( 'site-header' );
	if ( siteHeader ) {
		window.addEventListener( 'scroll', function () {
			if ( window.scrollY > 10 ) {
				siteHeader.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
			} else {
				siteHeader.style.boxShadow = '0 1px 3px rgba(0,0,0,0.05)';
			}
		}, { passive: true } );
	}

	// ============================================================
	// Smooth Scroll for Anchor Links
	// ============================================================
	document.querySelectorAll( 'a[href^="#"]' ).forEach( function ( anchor ) {
		anchor.addEventListener( 'click', function ( e ) {
			const targetId = this.getAttribute( 'href' ).slice( 1 );
			const target   = document.getElementById( targetId );
			if ( target ) {
				e.preventDefault();
				target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
		} );
	} );

	// ============================================================
	// Package / Delegate Type Selection (Registration)
	// ============================================================
	document.querySelectorAll( '[data-delegate-type]' ).forEach( function ( card ) {
		card.addEventListener( 'click', function () {
			document.querySelectorAll( '[data-delegate-type]' ).forEach( function ( c ) {
				c.classList.remove( 'is-selected' );
			} );
			this.classList.add( 'is-selected' );

			const type  = this.dataset.delegateType;
			const input = document.querySelector( 'input[name="delegate_type"]' );
			if ( input ) input.value = type;

			// Update the displayed price.
			const priceDisplay = document.getElementById( 'selected-price' );
			if ( priceDisplay && this.dataset.priceZar !== undefined ) {
				const zar = parseInt( this.dataset.priceZar, 10 );
				priceDisplay.textContent = zar === 0 ? 'Free' : 'R ' + zar.toLocaleString( 'en-ZA' );
			}
		} );
	} );

	// ============================================================
	// Auto-format phone inputs
	// ============================================================
	document.querySelectorAll( 'input[type="tel"]' ).forEach( function ( input ) {
		input.addEventListener( 'input', function () {
			this.value = this.value.replace( /[^\d\s\+\-\(\)]/g, '' );
		} );
	} );

	// ============================================================
	// Form validation feedback (HTML5 + custom styling)
	// ============================================================
	document.querySelectorAll( 'form.awsisa-form' ).forEach( function ( form ) {
		form.setAttribute( 'novalidate', '' );

		form.addEventListener( 'submit', function ( e ) {
			let valid = true;

			form.querySelectorAll( '[required]' ).forEach( function ( field ) {
				const group = field.closest( '.form-group' );
				if ( ! field.value.trim() ) {
					valid = false;
					field.classList.add( 'form-control--error' );
					if ( group ) {
						let errEl = group.querySelector( '.form-error' );
						if ( ! errEl ) {
							errEl = document.createElement( 'span' );
							errEl.className = 'form-error';
							group.appendChild( errEl );
						}
						errEl.textContent = 'This field is required.';
					}
				} else {
					field.classList.remove( 'form-control--error' );
					if ( group ) {
						const errEl = group.querySelector( '.form-error' );
						if ( errEl ) errEl.textContent = '';
					}
				}
			} );

			if ( ! valid ) {
				e.preventDefault();
				const firstError = form.querySelector( '.form-control--error' );
				if ( firstError ) firstError.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
		} );
	} );

	// ============================================================
	// Toast Notifications (lightweight, no library needed)
	// ============================================================
	window.awsisaToast = function ( message, type ) {
		type = type || 'info';
		const toast = document.createElement( 'div' );
		toast.className = 'notice notice--' + type;
		toast.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;max-width:380px;box-shadow:0 10px 30px rgba(0,0,0,0.15);animation:slideIn 0.2s ease;';
		toast.textContent = message;
		document.body.appendChild( toast );

		setTimeout( function () {
			toast.style.opacity = '0';
			toast.style.transition = 'opacity 0.3s';
			setTimeout( function () { toast.remove(); }, 300 );
		}, 4000 );
	};

} )();
