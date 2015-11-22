#E Requirements
We have some customer records in a text file (customers.json) -- one customer per line, JSON-encoded. We want to invite any customer within 100km of our Dublin office (GPS coordinates 53.3381985, -6.2592576) for some food and drinks on us.

Write a program that will read the full list of customers and output the names and user ids of matching customers (within 100km), sorted by user id (ascending).

You can use the first formula from this Wikipedia article to calculate distance: https://en.wikipedia.org/wiki/Great-circle_distance -- don't forget, you'll need to convert degrees to radians. Your program should be fully tested too.

Customer list is available here: https://gist.github.com/brianw/19896c50afa89ad4dec3

## Missing
- Documentation
