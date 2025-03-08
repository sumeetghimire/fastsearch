# FastSearch

FastSearch is a powerful Laravel package that accelerates search operations across multiple database models. It allows for flexible keyword searching with support for exact and partial matches, caching, and easy integration with Laravel applications.

## Features

- **Multi-model Search**: Search across multiple models with the same keyword.
- **Exact & Partial Matching**: Supports `LIKE` queries and exact matches.
- **Caching**: Built-in caching to improve performance.
- **Easy Integration**: Quick setup and simple to use in Laravel applications.
  
## Installation

To install the FastSearch package, run the following command:

```bash
composer require sumeetghimire/fastsearch


<h2>Example How to Use</h2>
<code><pre>
use SumeetGhimire\FastSearch\FastSearch;

public function search(Request $request)
{
    $keyword = $request->input('keyword', 'd');
    $userColumn = $request->input('user_column', 'name'); 
    $portfolioColumn = $request->input('portfolio_column', 'title'); 
    $returnColumnOnly = $request->input('return_column_only', false); 

    // Measure the execution time

    // Search in the User model
    $results = $this->search
        ->addModel(User::class, $userColumn, true)
        ->setKeyword($keyword)
        ->setMatchType('like') 
        ->setCacheDuration(600)
        ->search();


    return response()->json([
        'status' => 'success',
        'results' => $results,
    ]);
}
</pre></code>


<h2>License</h2>

The FastSearch package is open-source and licensed under the MIT License.

<h2>Credits</h2>
Author: Sumeet Ghimire
Email: sumeetghimire2526@gmail.com