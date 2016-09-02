## React Table Specification

Sorting - I will provide for every column a key to sort by, e.g. sort by key asc you need to call ?sort=keya for des. ?sort=keyd and so on. Keys I will output in a html anylike you need.

Pagination ?p=1. There will be a response in filter section from api where it will show current active page and other details

Search ?s=value 


Create setting:

form should consist all fields from the screenshot, serialize them and sent as a json to ./setting/add

The same it will be with edit, you need to sent it to ./setting/edit/<setting-id> id comes from response in _id field. 

For both above if there will be any error you will get a response with json consisting error key and a message  inside. Just show it in bootstrap red alert box.


Profile = drop down as filter
so if something is selected, then it executes the same request as search does by appending &profile=selectedvalue