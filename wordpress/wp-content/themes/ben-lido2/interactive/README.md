# Ben Lido: Frontend Development

## Technologies Used:

- node-sass: CSS Preprocessor
- postcss: CSS Vendor prefixes
- twigjs: JavaScript Templates
- BabelJS: Use latest JavaScript today
- Yarn: Node package manager
- ExpressJS: JavaScript/Node based web-server
- VsCode - Code Editor

## Frameworks

- Spectre: CSS Framework

### Development Instructions

- clone the repository by typing the following command in the terminal of your choice:

  `git clone git@bitbucket.org:bkd-digital/bdk-benlido.git`

- run the following 'seeder' script to generate categories and products dummy data:

  `yarn json --products=300`

- `yarn fed` - runs the local development environment and watches for any changes made to scss/css, twig, js files

#### If you use Hyper terminal or ZSH, you can cmd + click on the dev url presented in the terminal or you can manually type in: `http://localhost:3001`

# Amendments

### WooCommerce

- Needed to create overrides since we need to avoid customizing the page/component's template
- Keep WooCommerce scss files are modular as possible i.e. break out a given component's css into it's own .scss file
