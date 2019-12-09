# Amazon Partnertnet

Amazon Partnernet supports diffrent ways to get payed for advertisments on your site.

One of this methods is a specific Productlink. I decided to use this kind of 
advertisment so i can control which content to which page belongs.

There are two types of Productlinks. The Link iteself and a includable iframe wich
present the products image, title and price.

The second option has a issue with data privacy. Not only loading content from a
foreign site, Amazon is tracking your visitors massivly. 

For this reason you need a explicitly grant of the visitor to load this content - 
for other words, your visitor have to click or select what he we will allow according
to your data privacy rules.

This would be no Problem if you don't load third party content which you're not in 
charge of. So here is a solution for this part of Amazon Partnernet.

Link to the Amazon Partnernet: https://partnernet.amazon.de

# Sumedia Amazon Partnernet 

## Installation

Currently i'm not registered with wordpress. But if you interested you could have a
look on the github repository.

    composer require sumedia-wordpress/amapn
    
It has to be installed as a plugin under /wp-content/plugins/sumedia-amapn

## Usage

After the activation of the plugin there will appear a pluginpage under:

    Plugins > Sumedia Plugins
    
Search the Amazon Partnernet Plugin and click on configuration. You
will see there the table with already upsetted Ads or cou can add a link in the 
"Add Link" tab.

All you have to do is copy the URL from Amazong Partnernet Link into the URL field and 
set a frequency in which the data will be updated.

<small>A cronjob configuration will follow - it's not a good practice to load on page request.</small>

After you have submitted the URL the data will be parsed and stored. Now you can fetch the
WordPress Shortcode from the list.

You can add the Shortcode anywhere in your content, and the Amazon Ad will appear. 

<small>An implementation for theming the ad will follow - currently it suits to Amazon</small>  