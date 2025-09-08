const path = require('path');

const SyliusAdmin = require('@sylius-ui/admin');
const SyliusShop = require('@sylius-ui/shop');

const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));
const shopConfig = SyliusShop.getWebpackConfig(path.resolve(__dirname));

module.exports = [adminConfig, shopConfig];
