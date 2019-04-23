# UPCo Customizations
Below are customizations done by us

## Nested project directory
Everything is nested in the {PLUGIN_DIR}/blocks/{BLOCK_PROJECT_NAME} and is actually included with the PHP file in the main plugin directory.
Please add additional projects there.

## additional NPM packages
We added more dev dependencies to facilitate development.

```
npm install --save-dev babel-core babel-eslint babel-loader css-loader eslint @wordpress/babel-preset-default extract-text-webpack-plugin webpack webpack-cli
```

## Webpack
We are using Webpack instead of the default npm start and stop scripts that came with Guten Blocks.
We modified the package.json to build with webpack:

```
--- removed ---
  "scripts": {
    "start": "cgb-scripts start",
    "build": "cgb-scripts build",
    "eject": "cgb-scripts eject"
  },
--- added ---
  "scripts": {
    "start": "cross-env BABEL_ENV=default MODE=development webpack --watch",
    "build": "cross-env BABEL_ENV=default MODE=production webpack"
  },

```

We also added a .babelrc file.

# Default Guten Blocks Instructions

This project was bootstrapped with [Create Guten Block](https://github.com/ahmadawais/create-guten-block).

Below you will find some information on how to run scripts.

>You can find the most recent version of this guide [here](https://github.com/ahmadawais/create-guten-block).

## 👉  `npm start`
- Use to compile and run the block in development mode.
- Watches for any changes and reports back any errors in your code.

## 👉  `npm run build`
- Use to build production code for your block inside `dist` folder.
- Runs once and reports back the gzip file sizes of the produced code.

## 👉  `npm run eject`
- Use to eject your plugin out of `create-guten-block`.
- Provides all the configurations so you can customize the project as you want.
- It's a one-way street, `eject` and you have to maintain everything yourself.
- You don't normally have to `eject` a project because by ejecting you lose the connection with `create-guten-block` and from there onwards you have to update and maintain all the dependencies on your own.

---

###### Feel free to tweet and say 👋 at me [@MrAhmadAwais](https://twitter.com/mrahmadawais/)

[![npm](https://img.shields.io/npm/v/create-guten-block.svg?style=flat-square)](https://www.npmjs.com/package/create-guten-block) [![npm](https://img.shields.io/npm/dt/create-guten-block.svg?style=flat-square&label=downloads)](https://www.npmjs.com/package/create-guten-block)  [![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](https://github.com/ahmadawais/create-guten-block) [![Tweet for help](https://img.shields.io/twitter/follow/mrahmadawais.svg?style=social&label=Tweet%20@MrAhmadAwais)](https://twitter.com/mrahmadawais/) [![GitHub stars](https://img.shields.io/github/stars/ahmadawais/create-guten-block.svg?style=social&label=Stars)](https://github.com/ahmadawais/create-guten-block/stargazers) [![GitHub followers](https://img.shields.io/github/followers/ahmadawais.svg?style=social&label=Follow)](https://github.com/ahmadawais?tab=followers)
