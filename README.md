## Laravel Fixed Width File Importer

This Laravel console application provides an `import:file` Artisan command that allows parsing and importing records from a fixed-width text file (`PAYARC_DDF.txt`) into a MySQL database. Each line in the file represents a Record of a specific type, which has its own schema defined in a CSV file (`import_file_specs.csv`).

## Features

- 🚀 **Memory-efficient processing** using LazyCollections
- ⚙️ **Configurable schema** via CSV specifications
- 🔍 **Duplicate prevention** with unique import tracking
- 📊 **Progress tracking** with timestamps
- 🛠 **Robust error handling** with detailed logging

## Installation

1. Clone or download project from https://github.com/klg2362/import-file-app.git
2. Copy **.env.example** file and rename it as **.env**
3. Set up a **MySQL database** and configure the database connection in the **.env** file.
4. Open the console/terminal into the root directory of the project
5. Run `composer install` command
6. Run `php artisan key:generate`
7. Run the database migrations to create the required tables. Use `php artisan migrate` command

## Usage

``bash

`php artisan import:file {path_to_file} {id}`

### Example 

``bash

`php artisan import:file storage/app/imports/PAYARC_DDF file_20250702`

- `path_to_file`: Relative path under `storage/app/imports/`
- `id`: A unique identifier for the file being imported

## License

This project is open-source and available under the MIT License.
