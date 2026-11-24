/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';

const STATE_EXPANDED = 'expanded';
const STATE_COLLAPSED = 'collapsed';

/**
 * Collapses the sidebar to its icons on desktop, persists the state in a cookie
 * (read on the server to avoid any layout shift) and toggles it with Ctrl/Cmd+B.
 *
 * The navbar trigger is not a child of the sidebar: it reaches this controller through the "sidebar:*" window events.
 */
export default class extends Controller {
    static values = {
        cookieName: { type: String, default: 'sidebar_state' },
        cookieMaxAge: { type: Number, default: 60 * 60 * 24 * 7 },
        keyboardShortcut: { type: String, default: 'b' }
    };

    connect() {
        this.render();
    }

    toggle(event) {
        event?.preventDefault?.();

        if (!this.isDesktop()) {
            this.toggleMobileMenu();

            return;
        }

        this.setState(this.isCollapsed() ? STATE_EXPANDED : STATE_COLLAPSED);
    }

    open() {
        if (this.isDesktop()) {
            this.setState(STATE_EXPANDED);
        }
    }

    close() {
        if (this.isDesktop()) {
            this.setState(STATE_COLLAPSED);
        }
    }

    handleKeydown(event) {
        const shortcut = this.keyboardShortcutValue.toLowerCase();

        if (!shortcut || event.key?.toLowerCase() !== shortcut || (!event.ctrlKey && !event.metaKey)) {
            return;
        }

        const target = event.target;
        if (target?.isContentEditable || ['INPUT', 'SELECT', 'TEXTAREA'].includes(target?.tagName)) {
            return;
        }

        event.preventDefault();
        this.toggle();
    }

    /**
     * A collapsed sidebar only shows the first level of the menu:
     * clicking an item having children expands the sidebar with this item open.
     */
    expandFromMenu(event) {
        if (!this.isDesktop() || !this.isCollapsed()) {
            return;
        }

        const toggle = event.target.closest('[data-bs-toggle="dropdown"]');
        if (!toggle || !this.element.contains(toggle)) {
            return;
        }

        // Keeps Bootstrap from closing an already open item.
        event.preventDefault();
        event.stopPropagation();

        toggle.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.parentElement?.querySelector(':scope > .dropdown-menu')?.classList.add('show');

        this.open();
    }

    isCollapsed() {
        return STATE_COLLAPSED === this.element.dataset.sidebarState;
    }

    /**
     * The sidebar is fixed on the side of the page from the "navbar-expand-*" breakpoint,
     * below it is a regular navbar with a collapsible menu.
     */
    isDesktop() {
        return 'fixed' === window.getComputedStyle(this.element).position;
    }

    setState(state) {
        if (this.element.dataset.sidebarState === state) {
            return;
        }

        this.element.dataset.sidebarState = state;
        this.writeCookie(state);
        this.render();
    }

    render() {
        if (!this.element.id) {
            return;
        }

        const expanded = String(!this.isCollapsed());

        document.querySelectorAll(`[aria-controls="${this.element.id}"]`).forEach((trigger) => {
            trigger.setAttribute('aria-expanded', expanded);
        });
    }

    toggleMobileMenu() {
        const menu = this.element.querySelector('.navbar-collapse');

        if (menu && window.bootstrap?.Collapse) {
            window.bootstrap.Collapse.getOrCreateInstance(menu).toggle();
        }
    }

    writeCookie(value) {
        if (!this.cookieNameValue) {
            return;
        }

        document.cookie = `${this.cookieNameValue}=${encodeURIComponent(value)}; path=/; max-age=${this.cookieMaxAgeValue}; SameSite=Lax`;
    }
}
