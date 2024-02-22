// Importar Builder y Key de selenium-webdriver
const { Builder, By, Key, until } = require('selenium-webdriver');
const { BasePhpTest } = require("./BasePhpTest.js"); // Importar BasePhpTest
const { Select } = require('selenium-webdriver'); // Importar la clase Select
const assert = require('assert');


// Clase para la prueba de registro
class RegisterTest extends BasePhpTest {
         async test() {
                  try {
                           await this.driver.get("http://localhost:8000/register.php");

                           // Comprobar y completar los campos del formulario
                           let input_username = await this.driver.wait(until.elementLocated(By.id("nombre"), 1000));
                           assert(input_username, "ERROR TEST: input 'username' no encontrado");
                           await input_username.sendKeys("TestEnric");
                           await input_username.sendKeys(Key.ENTER);

                           let input_email = await this.driver.wait(until.elementLocated(By.id("mail")), 1000);
                           assert(input_email, "ERROR TEST: input 'mail' no encontrado");
                           await input_email.sendKeys("enric@test.com");
                           await input_email.sendKeys(Key.ENTER);

                           let input_password = await this.driver.wait(until.elementLocated(By.id("password")), 1000);
                           assert(input_password, "ERROR TEST: input 'password' no encontrado");
                           await input_password.sendKeys("P@ssw0rd!");
                           await input_password.sendKeys(Key.ENTER);

                           let input_password_confirm = await this.driver.wait(until.elementLocated(By.id("confirmarPassword")), 1000);
                           assert(input_password_confirm, "ERROR TEST: input 'confirmarPassword' no encontrado");
                           await input_password_confirm.sendKeys("P@ssw0rd!");
                           await input_password_confirm.sendKeys(Key.ENTER);

                           let input_country = new Select(await this.driver.wait(until.elementLocated(By.id("pais"))), 1000)
                           assert(input_country, "ERROR TEST: select 'country' no trobat")
                           await input_country.selectByVisibleText("Andorra")

                           let input_phone = await this.driver.wait(until.elementLocated(By.id("telefono")), 1000)
                           assert(input_phone, "ERROR TEST: input 'phone number' no encontrado")
                           await input_phone.sendKeys("121212121212")
                           await input_phone.sendKeys(Key.ENTER)

                           let input_city = await this.driver.wait(until.elementLocated(By.id("ciudad")), 1000)
                           assert(input_city, "ERROR TEST: input 'ciudad' no encontrado")
                           await input_city.sendKeys("Cornellà de Llobregat")
                           await input_city.sendKeys(Key.ENTER)

                           let input_postalCode = await this.driver.wait(until.elementLocated(By.id("codigoPostal")), 1000)
                           assert(input_postalCode, "ERROR TEST: input 'codigoPostal' no encontrado")
                           await input_postalCode.sendKeys("08940")
                           await input_postalCode.sendKeys(Key.ENTER)

                           // Hacer submit del formulario
                           let submit = await this.driver.wait(until.elementLocated(By.id("enviar-registro")), 1000)
                           assert(submit, "ERROR TEST: botón submit no encontrado")
                           await this.driver.actions()
                                    .move({ origin: submit })
                                    .click()
                                    .perform()

                           let notificationSuccess = false
                           try {
                                    let success_dialog = await this.driver.wait(until.elementLocated(By.css(".notification-container:last-child .success")), 5000)
                                    if (success_dialog) {
                                             notificationSuccess = true
                                    }
                           } catch (error) { }
                           assert(notificationSuccess, "ERROR TEST: register no completat")
                           console.log("TEST OK");
                  } catch (error) {
                           console.error("Error en la prueba:", error);
                  }
         }
}

// Ejecutar el test
(async function test_register() {
         const test = new RegisterTest();
         await test.run();
         console.log("END");
})();
