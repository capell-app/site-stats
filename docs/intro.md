Once you look after more than a handful of sites, it gets hard to say how many pages you have or how many domains are switched on. Site Stats counts them every hour and keeps one figure for each UTC day, so you can see whether the numbers are going up.

There are four totals. Pages counts every page that hasn't been deleted, drafts included. Sites counts the same way, and active sites are the ones switched on. Active domains are the switched-on domains attached to an active site. Only global administrators can see the figures, so someone who looks after a single site never sees totals for the rest.

The counting runs from your server's Laravel scheduler, which needs to be running. The last count of each day is the one kept. A count can only describe what exists right now, so a day the scheduler missed has no figure and can't be added later. The health check flags that, both before the first count and whenever 24 hours pass without one. Nothing about visitors is read and nothing leaves your server.

When the first count has run, the Site metrics page in the Capell admin shows a card for each total with its latest figure and a 30-day line.
