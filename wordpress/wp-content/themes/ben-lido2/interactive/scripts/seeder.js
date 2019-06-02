const faker = require("faker");
const fs = require("fs");
const args = require("yargs").argv;
const pathConfig = require("../config/paths");
const log = console.log;

const productCount = args.products || 2;
let originalProducts = [];
let originalCategories = [];
let categoryTitles = [
  "Bath and Body",
  "Beauty and Grooming",
  "Health",
  "Tools and Accessories"
];

let o;

for (o = 0; o < categoryTitles.length; o++) {
  const randomID = faker.random.uuid();
  let subCategories = [];

  const categoryEntry = {
    categoryID: randomID,
    categoryTitle: categoryTitles[o],
    href: `/categories/${randomID}`
  };

  // Create products for parent category
  let i;
  for (i = 0; i < productCount; i++) {
    const categoryID = randomID;
    const categoryTitle = categoryTitles[o];
    const uniqueID = faker.random.uuid();
    const sku = uniqueID;
    const name = faker.commerce.productName();
    const image = "/images/product-example.png";
    const description = faker.hacker.phrase().substr(0, 60) + "...";
    const href = `/product/${uniqueID}`;
    const price = faker.commerce.price();

    const result = {
      categoryID,
      categoryTitle,
      sku,
      name,
      image,
      description,
      href,
      price
    };

    originalProducts.push(result);
  }

  let k;
  for (k = 0; k < 4; k++) {
    const subEntry = {
      categoryID: faker.random.uuid(),
      parentCategory: randomID,
      categoryTitle: faker.commerce.productName(),
      href: `/categories/${randomID}`
    };

    subCategories.push(subEntry);
    categoryEntry.subs = subCategories;

    // Create products for sub category
    let i;
    for (i = 0; i < productCount; i++) {
      const uniqueID = faker.random.uuid();
      const categoryID = subEntry.categoryID;
      const categoryTitle = categoryTitles[o];
      const sku = uniqueID;
      const name = faker.commerce.productName();
      const image = "/images/product-example.png";
      const description = faker.hacker.phrase().substr(0, 60) + "...";
      const href = `/product/${uniqueID}`;
      const price = faker.commerce.price();

      const result = {
        categoryID,
        categoryTitle,
        sku,
        name,
        image,
        description,
        href,
        price
      };

      originalProducts.push(result);
    }
  }

  originalCategories.push(categoryEntry);
}

const products = JSON.stringify(originalProducts);
fs.writeFileSync(`${pathConfig.fakeData}/products.json`, products);

originalCategories.map(item => {
  const categoryID = item.categoryID;
  const featured = [];

  originalProducts.map(product => {
    if (product.categoryID === categoryID) {
      featured.push(product);
    }
  });
  item.featured = featured.splice(0, 3);
});

const categories = JSON.stringify(originalCategories);

fs.writeFileSync(`${pathConfig.fakeData}/categories.json`, categories);
