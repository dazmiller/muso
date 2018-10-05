import AbstractController from '../../common/controllers/abstract.controller';
import { generateStyles } from './configurations.theme.utils'

class AdminConfigurationsController extends AbstractController {
  constructor() {
    super(AdminConfigurationsController.$inject, arguments);
  }

  index() {
    this.tab = 'global';
    this.configs = {};
    this.theme = {};
    this.load();
  }

  setTab(tab) {
    this.tab = tab;
  }

  load() {
    this.Configuration.all()
      .then(({ configurations }) => {
        const configs = {};
        configurations.forEach((config) => {
          if (config.value === '1' || config.value === '0') {
            configs[config.key] = config.value === '1';
          } if (config.key === 'APP_CURRENT_THEME') {
            this.theme = JSON.parse(config.value);
          } else {
            configs[config.key] = config.value;
          }
        });

        this.configs = configs;
      });
  }

  save() {
    const configurations = Object.keys(this.configs).map(key => ({
      key,
      value: this.configs[key] ? this.configs[key] : false,
    }));

    configurations.push({ key: 'APP_CURRENT_THEME', value: JSON.stringify(this.theme) });

    this.Configuration.store({ configurations });
  }

  // Generates the css styles and temporarily injects them
  // into the header to preview changes.
  previewTheme() {
    const css = generateStyles(this.theme);
    const dom = document.getElementById('theme');

    dom.innerHTML = css;

    this.$mdToast.show({
      template: `<md-toast class="md-toast success"><i class="flaticon-checked21"></i> ${this.$translate.instant('CONFIG_THEME_UNSAVED')}</md-toast>`,
      hideDelay: 6000,
      position: 'top right'
    });
  }

  applyTheme() {
    const css = generateStyles(this.theme);

    this.Configuration.applyTheme(css)
      .then(() => {
        this.previewTheme();
      });
  }
  
  clearTheme() {
    var confirm = this.$mdDialog.confirm()
      .title(this.$translate.instant('ARE_YOU_SURE'))
      .textContent(this.$translate.instant('CONFIG_CLEAR_THEME'))
      .ariaLabel('Delete Theme')
      .targetEvent(event)
      .ok(this.$translate.instant('YES'))
      .cancel(this.$translate.instant('NO'));

    this.$mdDialog.show(confirm)
      .then(() => {
        this.Configuration.clearTheme()
          .then(() => {
            this.theme = {};
            this.previewTheme();
          });
      });
  }
}

AdminConfigurationsController.$inject = ['$state', '$mdToast', '$translate', '$mdDialog', 'Configuration'];

export default AdminConfigurationsController;