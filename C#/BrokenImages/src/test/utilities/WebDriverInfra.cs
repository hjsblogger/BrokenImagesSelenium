using System;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Edge;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium.IE;

// Inspiration - https://github.com/penblade/Tips/blob/master/Tips.Selenium/src/Test/WebDriverTest.cs

namespace OpenQA.Selenium
{
    internal static class WebDriverInfra
    {
        public static IWebDriver Create_Browser(BrowserType browserType)
        {
            switch (browserType)
            {
                case BrowserType.Chrome:
                    return new ChromeDriver();
                case BrowserType.Firefox:
                    return new FirefoxDriver();
                case BrowserType.Edge:
                    return new EdgeDriver();
                default:
                    throw new ArgumentOutOfRangeException(nameof(browserType), browserType, null);
            }
        }
    }
}
