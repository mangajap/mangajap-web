export interface JsonApiParams {
  include?: string | string[];
  fields?: {
    [type: string]: string | string[];
  };
  sort?: string | string[];
  limit?: number;
  offset?: number;
  filter?: {
    [type: string]: string | string[];
  };
}
