export interface OAuth2Body {
  access_token: string;
  token_type: string;
  sub?: string;
  expires_in?: number;
  refresh_token?: string;
  scope?: string;
}