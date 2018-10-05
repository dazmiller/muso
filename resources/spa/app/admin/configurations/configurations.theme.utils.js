
export function generateStyles(theme) {
  const styles = [];

  if (theme) {
    // Styles for main title
    if (theme.title) {
      if (theme.title.bg) {
        styles.push(`
          .main-sidenav .sidenav-menu-title {
            background-color: ${theme.title.bg} !important;
          }
        `);
      }
      if (theme.title.color) {
        styles.push(`
          .main-sidenav .sidenav-menu-title h1 {
            color: ${theme.title.color} !important;
          }
        `);
      }
      if (theme.title.iconColor) {
        styles.push(`
          .main-sidenav .sidenav-menu-title h1 i:before {
            color: ${theme.title.iconColor} !important;
          }
        `);
      }
    }

    // Main Menu
    if (theme.mainMenu) {
      if (theme.mainMenu.bg1) {
        styles.push(`
          .main-sidenav,
          .main-sidenav md-content .main-menu.menu-admin,
          .main-sidenav md-content {
            background-color: ${theme.mainMenu.bg1} !important;
          }
        `);
      }
      if (theme.mainMenu.bg2) {
        styles.push(`
          .main-sidenav md-content .main-menu.menu-admin,
          .main-sidenav md-content .menu-discovery,
          .main-sidenav .sidenav-menu-footer {
            background-color: ${theme.mainMenu.bg2} !important;
          }
        `);
      }
      if (theme.mainMenu.color) {
        styles.push(`
          .main-sidenav md-content .main-menu li a,
          .main-sidenav md-content .main-menu li.menu-title span,
          .main-sidenav md-content .playlists-menu li .add-playlist {
            color: ${theme.mainMenu.color} !important;
          }
        `);
      }
      if (theme.mainMenu.selected) {
        styles.push(`
          .main-sidenav md-content .main-menu li.active a i,
          .main-sidenav md-content .main-menu li.active a {
            color: ${theme.mainMenu.selected} !important;
          }
        `);
      }
    }

    // Main top toolbar
    if (theme.mainToolbar) {
      if (theme.mainToolbar.bg) {
        styles.push(`
          .main-viewport .main-content-toolbar,
          .main-progess-bar {
            background-color: ${theme.mainToolbar.bg} !important;
          }
        `);
      }
      if (theme.mainToolbar.color) {
        styles.push(`
          md-menu-bar button {
            color: ${theme.mainToolbar.color} !important;
          }
        `);
      }
      if (theme.mainToolbar.iconColor) {
        styles.push(`
          .main-viewport .main-content-toolbar .main-actions .md-menu i:before {
            color: ${theme.mainToolbar.iconColor} !important;
          }
        `);
      }
    }

    if (theme.common) {
      if (theme.common.mainBg) {
        styles.push(`
          body,
          md-content,
          md-sidenav,
          md-dialog,
          .auth-full,
          .main-viewport .main-view {
            background-color: ${theme.common.mainBg} !important;
          }
        `)
      }
      if (theme.common.containersBg) {
        styles.push(`
          .admin-grid-panel,
          .admin-panel,
          md-card,
          md-tabs-content-wrapper,
          md-tab-item.md-tab.md-active,
          .white-panel {
            background-color: ${theme.common.containersBg} !important;
          }
        `)
      }
      if (theme.common.sidebarBg) {
        styles.push(`
          .main-viewport .main-view .common-sidebar,
          .admin-actions-sidebar {
            background-color: ${theme.common.sidebarBg} !important;
          }
        `)
      }
      if (theme.common.color) {
        styles.push(`
          .main-viewport .main-view,
          .main-viewport .main-view .common-sidebar,
          ul.comments-list li.comments-item .comments-item-text h3,
          md-list md-list-item.md-3-line .md-list-item-text h3,
          md-list md-list-item.md-3-line .md-list-item-text p,
          ul.comments-list li.comments-item .comments-item-text p,
          .admin-actions-sidebar {
            color: ${theme.common.color} !important;
          }
        `)
      }
      if (theme.common.linkColor) {
        styles.push(`
          .main-viewport .main-view a,
          .main-viewport .main-view .common-sidebar a {
            color: ${theme.common.linkColor} !important;
          }
        `)
      }
      if (theme.common.linkHoverColor) {
        styles.push(`
          .main-viewport .main-view a:hover,
          .main-viewport .main-view .common-sidebar a:hover {
            color: ${theme.common.linkHoverColor} !important;
          }
        `)
      }
    }

    if (theme.forms) {
      if (theme.forms.bg) {
        styles.push(`
          .main-viewport .main-content-toolbar .main-search input,
          md-input-container input.md-input, md-input-container textarea.md-input {
            background-color: ${theme.forms.bg} !important;
          }
        `);
      }
      if (theme.forms.border) {
        styles.push(`
          .main-viewport .main-content-toolbar .main-search input,
          md-input-container input.md-input, md-input-container textarea.md-input {
            border: 1px solid ${theme.forms.border} !important;
            box-shadow: 0 1px 0 0 ${theme.forms.border} !important;
          }
        `);
        styles.push(`
          md-select .md-select-value,
          .md-datepicker-input-container {
            border-bottom-color: ${theme.forms.border} !important;
          }
        `);
      }
      if (theme.forms.color) {
        styles.push(`
          md-select-menu md-option .md-text,
          md-datepicker .md-datepicker-input,
          md-datepicker md-icon,
          .main-viewport .main-content-toolbar .main-search input,
          md-input-container input.md-input, md-input-container textarea.md-input {
            color: ${theme.forms.color} !important;
          }
        `);
        styles.push(`
          .md-datepicker-triangle-button .md-datepicker-expand-triangle {
            border-top-color: ${theme.forms.color} !important;
          }
        `);
      }
      if (theme.forms.placeholderColor) {
        styles.push(`
          md-input-container:not(.md-input-invalid).md-input-has-value label {
            color: ${theme.forms.placeholderColor} !important;
          }
        `);
      }
    }

    if (theme.buttons) {
      if (theme.buttons.primaryBg) {
        styles.push(`
          .md-button.md-primary.md-raised:not([disabled]),
          .md-button.md-primary,
          .md-button.md-default-theme.md-primary {
            background-color: ${theme.buttons.primaryBg} !important;
            border-color: ${theme.buttons.primaryBg} !important;
          }
        `);
      }
      if (theme.buttons.privaryOverBg) {
        styles.push(`
          .md-button.md-primary.md-raised:not([disabled]):hover {
            background-color: ${theme.buttons.privaryOverBg} !important;
            border-color: ${theme.buttons.privaryOverBg} !important;
          }
        `);
      }
      if (theme.buttons.actionBg) {
        styles.push(`
          a.md-button.md-warn.md-raised:not([disabled]),
          .md-button.md-warn.md-raised:not([disabled]) {
            background-color: ${theme.buttons.actionBg} !important;
            border-color: ${theme.buttons.actionBg} !important;
          }
        `);
      }
      if (theme.buttons.actionOverBg) {
        styles.push(`
          a.md-button.md-warn.md-raised:not([disabled]):hover,
          .md-button.md-warn.md-raised:not([disabled]):hover {
            background-color: ${theme.buttons.actionOverBg} !important;
            border-color: ${theme.buttons.actionOverBg} !important;
          }
        `);
      }
      if (theme.buttons.defaultBg) {
        styles.push(`
          a.md-button.md-raised:not([disabled]),
          .md-button.md-raised:not([disabled]) {
            background-color: ${theme.buttons.defaultBg} !important;
            border-color: ${theme.buttons.defaultBg} !important;
          }
        `);
      }
      if (theme.buttons.defaultOverBg) {
        styles.push(`
          a.md-button.md-raised:not([disabled]):hover,
          .md-button.md-raised:not([disabled]):hover {
            background-color: ${theme.buttons.defaultOverBg} !important;
            border-color: ${theme.buttons.defaultOverBg} !important;
          }
        `);
      }
    }

    if (theme.player) {
      if (theme.player.bg) {
        styles.push(`
          .main-viewport .main-player,
          .main-viewport .main-content-player {
            background-color: ${theme.player.bg} !important;
          }
        `);
      }
      if (theme.player.color) {
        styles.push(`
          .main-viewport .main-content-player .videogular-container .controls-container .iconButton:before,
          .main-viewport .main-content-player .videogular-container .controls-container vg-time-display,
          .main-viewport .main-player .main-content-player-info p {
            color: ${theme.player.color} !important;
          }
        `);
        styles.push(`
          .main-viewport .main-content-player videogular vg-scrub-bar-current-time,
          .main-viewport .main-content-player [videogular] vg-scrub-bar-current-time {
            background-color: ${theme.player.color} !important;
          }
        `);
      }
      if (theme.player.buttonsOverBg) {
        styles.push(`
          .main-viewport .main-content-player .videogular-container .controls-container .iconButton:hover:enabled,
          .main-viewport .main-content-player videogular vg-scrub-bar [role=slider],
          .main-viewport .main-player .main-content-player-info:hover {
            background-color: ${theme.player.buttonsOverBg} !important;
          }
        `);
      }
    }

    if (theme.tags) {
      if (theme.tags.bg) {
        styles.push(`
            .main-viewport .main-view .tags a {
              background-color: ${theme.tags.bg} !important;
            }
          `);
      }
      if (theme.tags.color) {
        styles.push(`
            .main-viewport .main-view .tags a {
              color: ${theme.tags.color} !important;
            }
          `);
      }
    }

    if (theme.custom) {
      styles.push(theme.custom);
    }
  }

  return styles.join(' ');
}