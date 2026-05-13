import path from 'path';
import { fileURLToPath } from 'url';
import SyliusAdmin from '@sylius-ui/admin';
import SyliusShop from '@sylius-ui/shop';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default async () => {
    const adminConfig = await SyliusAdmin.getWebpackConfig(path.resolve(__dirname));
    const shopConfig = await SyliusShop.getWebpackConfig(path.resolve(__dirname));

    return [adminConfig, shopConfig];
};
