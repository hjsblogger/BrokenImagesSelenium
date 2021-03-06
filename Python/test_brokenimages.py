import requests
import urllib3
import pytest
from requests.exceptions import MissingSchema, InvalidSchema, InvalidURL
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys

capabilities = {
    "build": "[Python] Finding broken images on a webpage using Selenium",
    "name": "[Python] Finding broken images on a webpage using Selenium",
    "platform": "Windows 10",
    "browserName": "Chrome",
    "version": "latest"
}

user_name = "user-name"
app_key = "access-key"
URL = "https://the-internet.herokuapp.com/broken_images"
iBrokenImageCount = 0

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
remote_url = "http://" + user_name + ":" + app_key + "@hub.lambdatest.com/wd/hub"
driver = webdriver.Remote(command_executor=remote_url, desired_capabilities=capabilities)
driver.maximize_window()
driver.get(URL)

image_list = driver.find_elements(By.TAG_NAME, "img")
print('Total number of images on '+ URL + ' are ' + str(len(image_list)))

for img in image_list:
    try:
        response = requests.get(img.get_attribute('src'), stream=True)
        if (response.status_code != 200):
            print(img.get_attribute('outerHTML') + " is broken.")
            iBrokenImageCount = (iBrokenImageCount + 1)

    except requests.exceptions.MissingSchema:
        print("Encountered MissingSchema Exception")
    except requests.exceptions.InvalidSchema:
        print("Encountered InvalidSchema Exception")
    except:
        print("Encountered Some other execption")

driver.quit()

print('The page ' + URL + ' has ' + str(iBrokenImageCount) + ' broken images')